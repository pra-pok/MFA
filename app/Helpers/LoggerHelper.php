<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Carbon\Carbon;

if (!function_exists('teamUserLogger')) {
    function teamUserLogger($teamId, $userId) {

        $currentDate = Carbon::now()->toDateString();
        $logPathTemplate = base_path('logs_file/team_logs/team_{team_id}_user_{user_id}-{date}.log');
//        $logPathTemplate = '/home/Official/LaravelProjects/MFA_Logs/team_logs/team_{team_id}_user_{user_id}-{date}.log';
        $logPath = str_replace(
            ['{team_id}', '{user_id}', '{date}'],
            [$teamId, $userId, $currentDate],
            $logPathTemplate
        );

        $directory = dirname($logPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $logger = new Logger("team_{$teamId}_user_{$userId}");
        $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));

        return $logger;
    }
}

