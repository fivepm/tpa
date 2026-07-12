<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fonnte WhatsApp API
    |--------------------------------------------------------------------------
    | Token didapat dari dashboard Fonnte: https://fonnte.com
    | Format target nomor: 628xxxxxxxxx (tanpa + dan spasi)
    */

    'token' => env('FONNTE_TOKEN', ''),
    'url'   => 'https://api.fonnte.com/send',
];
