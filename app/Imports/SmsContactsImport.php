<?php

namespace App\Imports;

use App\Models\SmsLog;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;

class SmsContactsImport implements ToCollection
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            SmsLog::create([
                'recipients' => $row[0], // Assuming first column is phone number
                'message' => request()->input('message'), // Get message from request
                'status' => 'pending',
                'vendor' => request()->input('vendor'),
                'organization_id' => request()->input('organization_id'),
            ]);
        }
    }
}
