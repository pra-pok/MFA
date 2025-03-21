<?php

namespace App\Helpers;
use Ramsey\Uuid\Uuid;
use App\Models\SmsApiToken;
class MfaHelper
{
    public static function generateSecret()
    {
        $secret = '';
        $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $secretLength = 16;
        for ($i = 0; $i < $secretLength; $i++) {
            $secret .= $validChars[rand(0, strlen($validChars) - 1)];
        }
        return $secret;
    }

    public static function generateQRCode($secret, $name, $issuer)
    {
        $url = 'otpauth://totp/' . $name . '?secret=' . $secret . '&issuer=' . $issuer;
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($url);
    }

    public static function generateUUID()
    {
        return Uuid::uuid4()->toString();
    }

}
