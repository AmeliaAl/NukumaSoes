<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (app(\Filament\FilamentManager::class)->getPanels() as $panel) {
    echo "Panel ID: " . $panel->getId() . "\n";
    echo "Resources:\n";
    foreach ($panel->getResources() as $resource) {
        echo " - " . $resource . "\n";
    }
    echo "\n";
}
