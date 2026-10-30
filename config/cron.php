<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cron Webhook Token
    |--------------------------------------------------------------------------
    |
    | Secret token used to authenticate webhook calls from external cron services
    | such as cron-job.org when server-level cron/crontab is not accessible.
    |
    */
    'token' => env('CRON_WEBHOOK_TOKEN'),
];
