<?php

namespace App\Dtos;

use Illuminate\Support\Facades\Date;

class ResponseDTO implements \JsonSerializable
{

    protected $data;
    protected $message;
    protected $status;

    public function __construct($data, $message, $status)
    {
        $this->data = $data;
        $this->message = $message;
        $this->status = $status;
    }

    // Implement the JsonSerializable interface
    public function jsonSerialize()
    {
        return [
            'data' => $this->data,
            'message' => $this->message,
            'status' => $this->status,
            'timestamp' => now()
        ];
    }


}
