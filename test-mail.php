<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    \Mail::raw('Test email depuis le serveur', function ($message) {
        $message->to('jnt.marois@gmail.com')->subject('Test Workshop Pilot');
    });
    echo "OK - Email envoyé\n";
} catch (\Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}
