<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WhatsAppContactsImport;

class WhatsAppMessagesController extends DM_BaseController
{
    protected $panel = 'History';
    protected $base_route = 'admin.whatsapp-messages';
    protected $view_path = 'admin.components.whatsapp_messages';
    protected $model;
    protected $table;
    protected $folder = 'whatsapp_messages';
    protected $whatsappService;

    public function __construct(Request $request, WhatsAppMessage $whatsapp_message,WhatsAppService $whatsappService)
    {
        $this->model = $whatsapp_message;
        $this->whatsappService = $whatsappService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->with(['createds' => function($query) {
                $query->select('id', 'username');
            }, 'updatedBy' => function($query) {
                $query->select('id', 'username');
            }])->orderBy('created_at', 'desc')->get();
            return response()->json($data);
        }
        return view(parent::loadView($this->view_path . '.index'));
    }

    public function create(Request $request)
    {
        return view(parent::loadView($this->view_path . '.create'));
    }
    
  public function sendTextMessage(Request $request)
{
    $request->validate([
        'message' => 'required|string',
        'recipient_phone' => 'nullable|array',
        'recipient_phone.*' => 'nullable|string',
        'group_audience' => 'nullable|array',
        'group_audience.*' => 'nullable|string',
        'contacts' => 'nullable|file',
    ]);

    try {
        \DB::beginTransaction();

        $phones = [];

        if (!empty($request->recipient_phone)) {
            $phones = array_merge($phones, array_map('trim', $request->recipient_phone));
        }

        if ($request->hasFile('contacts')) {
            $importedNumbers = $this->importContacts($request->file('contacts'));

            if (!is_array($importedNumbers)) {
                throw new \Exception('Error importing contacts.');
            }

            $phones = array_merge($phones, $importedNumbers);
        }

        $phones = array_unique(array_filter($phones));
        if (empty($phones)) {
            throw new \Exception('No valid phone numbers found.');
        }

        // Send messages
        $message = strip_tags($request->message);
        $responses = [];

        foreach ($phones as $phone) {
            $responses[$phone] = app(WhatsAppService::class)->sendMessage($phone, $message);
        }

        \DB::commit();

        logUserAction(
            auth()->user()->id,
            auth()->user()->team_id,
            'WhatsApp messages sent successfully!',
            ['recipients' => $phones]
        );

        $request->session()->flash('alert-success', 'Messages sent successfully!');

        return $request->ajax() 
            ? response()->json(['success' => true, 'message' => 'Messages sent successfully!', 'recipients' => $phones, 'responses' => $responses])
            : redirect()->back();

    } catch (\Exception $exception) {
        \DB::rollBack(); 
        \Log::error('WhatsApp Message Sending Error: ' . $exception->getMessage());

        $request->session()->flash('alert-danger', 'Error: ' . $exception->getMessage());

        return $request->ajax() 
            ? response()->json(['success' => false, 'message' => 'Error: ' . $exception->getMessage()], 500)
            : redirect()->back();
    }
}

    // private function getNumbersFromGroups($groupIds)
    // {
    //     return \DB::table('organization_signup')
    //         ->whereIn('group_id', $groupIds)
    //         ->pluck('phone_number')
    //         ->toArray();
    // }

    public function importContacts($file)
    {
        try {
            $import = new WhatsAppContactsImport();
            Excel::import($import, $file);
            
            $importedNumbers = $import->getImportedNumbers();
            if (empty($importedNumbers)) {
                return []; 
            }
            return $importedNumbers;
        } catch (\Exception $e) {
            \Log::error('Import Error: ' . $e->getMessage());
            return []; 
        }
    }
}
