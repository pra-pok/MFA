<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
//
//if (!function_exists('teamUserLogger')) {
//    function teamUserLogger($teamId, $userId) {
//        $currentDate = Carbon::now()->toDateString();
//        $logPathTemplate = base_path('logs_file/team_logs/team_' . $teamId . '_user_' . $userId . '-' . $currentDate . '.log');
//
//        $directory = dirname($logPathTemplate);
//        if (!file_exists($directory)) {
//            mkdir($directory, 0755, true);
//        }
//
//        $logger = new Logger("team_{$teamId}_user_{$userId}");
//        $logger->pushHandler(new StreamHandler($logPathTemplate, Logger::DEBUG));
//
//        return $logger;
//    }
//}

if (!function_exists('logUserAction')) {
    function logUserAction($userId, $teamId, $message, $context = [])
    {
        $currentDate = Carbon::now()->toDateString();
        $autoKey = generateAutoKey($teamId, $userId);

        $logDirectory = '/home/edigitalnepal/Official/data/log';
        $filePath = "{$logDirectory}/team_{$autoKey}-{$currentDate}.log";

        // Ensure the directory exists
        if (!is_dir($logDirectory)) {
            if (!mkdir($logDirectory, 0755, true) && !is_dir($logDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" could not be created', $logDirectory));
            }
        }
        $logChannel = Log::build([
            'driver' => 'single',
            'path' => $filePath,
            'level' => 'debug',
        ]);

        $logChannel->info($message, $context);
    }
}
