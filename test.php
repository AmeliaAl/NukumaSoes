<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = App\Models\User::where('email', 'admin@gmail.com')->first();
echo $u->role . '|' . ($u->isAdmin() ? 'true' : 'false');
