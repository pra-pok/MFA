<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\DM_BaseController;
use App\Mail\Sendemail;
use App\Models\Emailtracking;
use App\Models\Organization;
use App\Models\Organizationemailconfig;
use App\Models\OrganizationSignup;
use App\Services\MailConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OrganizationemailController extends DM_BaseController
{
    protected $panel = 'Organization Email';
    protected $base_route = 'organizationemail';
    protected $view_path = 'admin.components.email';
    protected $model;
    protected $table;
    protected $folder = 'organizationemail';


    public function __construct(Organizationemailconfig $organizationemailconfig)
    {
        $this->model = $organizationemailconfig;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->with(['createds' => function ($query) {
                $query->select('id', 'username');
            }, 'updatedBy' => function ($query) {
                $query->select('id', 'username');
            }])->orderBy('created_at', 'desc')->get();
            return response()->json($data);
        }
        return view(parent::loadView($this->view_path . '.index'));
    }
    public function create()
    {
        return view(parent::loadView($this->view_path . '.create'));
    }

    public function search(Request $request)
    {
        try {
            $query = $request->q;
            $organizations = OrganizationSignup::where('full_name', 'LIKE', "%{$query}%")
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->select('id', 'full_name')
                ->limit(1)
                ->get();
            return response()->json($organizations);
        } catch (\Exception $e) {
            \Log::error('' . $e->getMessage());
        }
    }


    public function store(Request $request)
    {

        try {
            $validatedData =  $request->validate([
                'organization_signup_id' => 'required|exists:organization_signup,id',
                'user_emails' => 'nullable|array|min:1',
                'user_emails.*' => 'nullable|string|min:1|email',
                'message' => 'required|string',
                'subject' => 'required|string|max:191',
                'user_emails_file' => 'nullable|mimes:csv,xlsx|max:4096',

            ]);
            $message = $validatedData['message'];
            $subject = $validatedData['subject'];
            $organization  = OrganizationSignup::where('id', $validatedData['organization_signup_id'])->first();
            if (empty($organization)) {
                request()->session()->flash('alert-success', $this->panel . ' Sorry! You have selected invalid organization');
                return redirect()->route('admin.whatsapp-messages.create');
            }
            $emails = [];
            if (isset($validatedData['user_emails'])) {
                $emails = $validatedData['user_emails'];
            }
            if (isset($validatedData['user_emails_file'])) {
                $fileEmails = $this->processUploadedFile($validatedData['user_emails_file']);
                if ($fileEmails != false) {
                    $emails = array_merge($emails, $fileEmails['valid']);
                    $invalidEmails = $fileEmails['invalid'];
                } else {
                    request()->session()->flash('alert-error', $this->panel . ' There was error while reading the file. Please check the file persmission');
                    return redirect()->route('admin.whatsapp-messages.create');
                }
            }

            if (isset($validatedData['user_custom_list'])) {
                $emails = array_merge($emails, $validatedData['user_custom_list']);
            }

            if (empty($emails)) {
                request()->session()->flash('alert-error', $this->panel . ' No valid Emails found');
                return redirect()->route('admin.whatsapp-messages.create');
            }
            $batch = $this->getNextBatchNumber();

            if (!empty($emails)) {
                $this->sendandstoreemail($emails, $subject, $message, true, $batch, $validatedData['organization_signup_id']);
            }
            if (!empty($invalidEmails)) {
                $this->sendandstoreemail($invalidEmails, $subject, $message, true, $batch, $validatedData['organization_signup_id']);
            }
            request()->session()->flash('alert-success', $this->panel . ' Email Sent Successfully');
            return redirect()->route('admin.whatsapp-messages.create');
        } catch (ValidationException $e) {
            request()->session()->flash('alert-error',  'Internal Server Error occurred.');
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $th) {
            \Log::error($th->getMessage());
            request()->session()->flash('alert-error', $this->panel . 'Internal Server Error');
            return redirect()->route('admin.whatsapp-messages.create');
        }
    }

    private function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    private function sendemail($email, $subject, $message, $organization)
    {
        try {
            // (new MailConfigService)->setMailConfig($organization);
            Mail::to($email)->queue(new Sendemail($subject, $message));
            return true;
        } catch (\Exception $ex) {
            Log::info("Exception on sending email. Message" . $ex->getMessage());
            return false;
        } catch (\Error $er) {
            Log::info("Error on sending email. Message" . $er->getMessage());
            return false;
        }
    }

    private function getNextBatchNumber()
    {
        $lastEntry = Emailtracking::orderBy('created_at', 'desc')->first();
        return $lastEntry ? $lastEntry->batch + 1 : 1;
    }

    private function processUploadedFile($file)
    {
        $data = $this->csvToArray($file);
        if ($data === false) {
            return false;
        }

        $validEmails = [];
        $invalidEmails = [];

        foreach ($data as $row) {
            $email = $row['email'] ?? $row['Email'] ?? null;
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            } else {
                $invalidEmails[] = $email;
            }
        }

        return ['valid' => $validEmails, 'invalid' => $invalidEmails];
    }

    public function sendandstoreemail($emails, $subject, $message, $status, $batch, $organization_signup_id)
    {

        $organization =  Organization::first($organization_signup_id);
        foreach ($emails as $email) {
            if ($status == true) {
                $emailsent = $this->sendemail($email, $subject, $message, $organization);
            }
            $data['email'] = $email;
            $data['subject'] = $subject;
            $data['message'] = $message;
            $data['organization_signup_id'] = $organization_signup_id;

            if (isset($emailsent)) {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }
            $data['batch'] = $batch;
            Emailtracking::create($data);
        }
    }
}
