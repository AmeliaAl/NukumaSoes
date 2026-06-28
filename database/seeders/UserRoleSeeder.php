<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing users to admin (if any)
        User::whereNull('role')->orWhere('role', '')->update(['role' => 'admin']);

        // Create admin user if not exists
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
            $this->command->info('✅ Admin user created: admin@example.com / password');
        }

        // Create pemilik user if not exists
        if (!User::where('email', 'pemilik@example.com')->exists()) {
            User::create([
                'name' => 'Pemilik',
                'email' => 'pemilik@example.com',
                'password' => Hash::make('password'),
                'role' => 'pemilik',
            ]);
            $this->command->info('✅ Pemilik user created: pemilik@example.com / password');
        }

        if (!User::where('email', 'aset@example.com')->exists()) {
            User::create([
                'name' => 'Admin Aset',
                'email' => 'aset@example.com',
                'password' => Hash::make('password'),
                'role' => 'aset',
            ]);
            $this->command->info('✅ Admin Aset user created: aset@example.com / password');
        }

        $this->command->info('🎉 User roles seeded successfully!');
    }
}
