<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_to_first_admin_setup_when_no_admin_exists(): void
    {
        $this->get('/login')
            ->assertRedirect(route('setup.first-admin'));
    }

    public function test_login_screen_can_be_rendered_when_admin_exists(): void
    {
        $this->createAdmin();

        $this->get('/login')
            ->assertOk()
            ->assertViewIs('auth.login');
    }

    public function test_admin_can_login_and_logout(): void
    {
        $admin = $this->createAdmin();

        $this->post('/login', [
            'username' => $admin->username,
            'password' => 'password',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($admin, 'admin');

        $this->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest('admin');
    }

    public function test_admin_cannot_login_with_invalid_password(): void
    {
        $admin = $this->createAdmin();

        $this->post('/login', [
            'username' => $admin->username,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('username');

        $this->assertGuest('admin');
    }

    private function createAdmin(): Admin
    {
        return Admin::create([
            'nama_lengkap' => 'Admin Produksi',
            'username' => 'adminproduksi',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'aktif',
        ]);
    }
}
