<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class WhatsAppContactsImport implements ToCollection
{
    private $importedNumbers = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue;
            }
            $contactNumber = trim($row[1]);

            if ($this->isValidPhoneNumber($contactNumber)) {
                $this->importedNumbers[] = $contactNumber;
            }
        }
    }

    public function getImportedNumbers()
    {
        return array_unique($this->importedNumbers); 
    }

    private function isValidPhoneNumber($phone)
    {
        return !empty($phone) && preg_match('/^\+?[0-9]{10,15}$/', $phone);
    }
}