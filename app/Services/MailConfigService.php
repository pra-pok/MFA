<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailConfigService
{
    public function setMailConfig($organization)
    {
        // Example: Fetch organization mail settings from the database
        $mailConfig = [
            'MAIL_MAILER' => $organization->mail_driver,
            'MAIL_HOST' => $organization->mail_host,
            'MAIL_PORT' => $organization->mail_port,
            'MAIL_USERNAME' => $organization->mail_username,
            'MAIL_PASSWORD' => $organization->mail_password,
            'MAIL_ENCRYPTION' => $organization->mail_encryption,
            'MAIL_FROM_ADDRESS' => $organization->mail_from_address,
            'MAIL_FROM_NAME' => $organization->mail_from_name,
        ];

        foreach ($mailConfig as $key => $value) {
            Config::set($key, $value);
        }
    }
}
