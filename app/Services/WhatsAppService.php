<?php
namespace App\Services;

use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $accessToken;
    private $apiVersion;
    private $phoneNumberId;

    public function __construct()
    {
        $this->accessToken = env('WHATSAPP_ACCESS_TOKEN');
        $this->apiVersion = 'v17.0'; // Ensure this matches the latest Meta API version
        $this->phoneNumberId = env('WHATSAPP_PHONE_NUMBER_ID');
    }

    public function sendMessage($to, $message)
    {
        try {
            // Ensure WhatsApp phone number is in the correct format (E.164 format)
            $to = preg_replace('/[^0-9]/', '', $to); // Remove non-numeric characters
            if (empty($to)) {
                throw new \Exception("Invalid phone number format.");
            }

            // Save to database with pending status
            $whatsappMessage = WhatsAppMessage::create([
                'recipient_phone' => $to,
                'message' => $message,
                'status' => 'pending',
            ]);

            $apiUrl = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            // Send message via WhatsApp API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => $message // Ensure 'body' is used instead of 'message'
                ]
            ]);

            $responseData = $response->json();
            Log::info("WhatsApp API Response: " . json_encode($responseData));

            if ($response->successful() && isset($responseData['messages'])) {
                $whatsappMessage->update(['status' => 'sent']);
                return [
                    'success' => true,
                    'message_id' => $responseData['messages'][0]['id'] ?? null,
                    'recipient' => $to,
                ];
            } else {
                $whatsappMessage->update(['status' => 'failed']);
                Log::error("WhatsApp API Error: " . json_encode($responseData));
                return [
                    'success' => false,
                    'error' => $responseData['error']['message'] ?? 'Unknown error',
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsApp Service Exception: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}