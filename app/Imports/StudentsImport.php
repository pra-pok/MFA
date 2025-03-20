<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class StudentsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Get the authenticated organization/tenant
        $organization = Auth::user();
        // Check if the student already exists with the same tenant_id, name, email, phone, and address
        $existingStudent = Student::where('tenant_id', $organization->tenant_id)
            ->where('name', $row['name'])
            ->where('email', $row['email'])
            ->where('phone', $row['phone'])
            ->where('address', $row['address'])
            ->first();
        // If the student exists, skip this row
        if ($existingStudent) {
            return null;
        }
        // Otherwise, create a new student record
        return new Student([
            'tenant_id' => $organization->tenant_id,
            'name'      => $row['name'],
            'email'     => $row['email'],
            'phone'     => $row['phone'],
            'address'   => $row['address'],
        ]);
    }
}
