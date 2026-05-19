<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$user = User::firstOrCreate(
    ['email' => 'admin@gmail.com'],
    [
        'name' => 'Admin User',
        'role' => 'admin',
        'password' => Hash::make('password'),
    ]
);

echo "Admin user created/verified: " . $user->email . " (password: password)\n";
echo "Login now!\n";
?>

