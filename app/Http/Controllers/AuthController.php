<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AuthController extends Controller
{
    /**
     * Show login form (atau redirect ke setup kalau belum ada admin)
     */
    public function showLogin()
    {
        // Cek apakah sudah ada admin
        if (Admin::count() == 0) {
            return redirect()->route('setup.first-admin');
        }
        
        return view('auth.login');
    }

    /**
     * Process login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string|max:255',
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah!',
        ])->withInput($request->only('username'));
    }

    /**
     * Logout - UPDATED dengan cache prevention
     */
    public function logout(Request $request)
    {
        // Logout dari guard admin
        Auth::guard('admin')->logout();
        
        // Invalidate session untuk mencegah session reuse
        $request->session()->invalidate();
        
        // Regenerate CSRF token untuk keamanan
        $request->session()->regenerateToken();
        
        // Redirect ke login dengan headers no-cache
        return redirect('/login')
            ->with('success', 'Berhasil logout!')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Process change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|max:255',
            'new_password' => 'required|string|min:6|max:255|confirmed',
        ], [
            'current_password.required' => 'Password lama harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah!']);
        }

        $admin->password = Hash::make($request->new_password);
        $admin->save();

        return back()->with('success', 'Password berhasil diubah!');
    }

    /**
     * Tampilkan form edit profil
     */
    public function showProfile()
    {
        $admin = Auth::guard('admin')->user();
        return view('auth.profile', compact('admin'));
    }

    /**
     * Update data profil admin
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_num|unique:admins,username,' . $admin->id_admin . ',id_admin',
        ], [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'username.required' => 'Username harus diisi',
            'username.alpha_num' => 'Username hanya boleh huruf dan angka',
            'username.unique' => 'Username sudah digunakan oleh akun lain',
        ]);

        $admin->update([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Tampilkan pengaturan sistem / metadata
     */
    public function showSettings()
    {
        $systemInfo = [
            'app_name' => 'Nukuma Cantique',
            'app_version' => '1.2.0',
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'db_driver' => config('database.default'),
            'env' => config('app.env'),
            'timezone' => config('app.timezone'),
            'counts' => [
                'akun' => \App\Models\Akun::count(),
                'bahan_baku' => \App\Models\BahanBaku::count(),
                'tenaga_kerja' => \App\Models\TenagaKerja::count(),
                'produk' => \App\Models\Produk::count(),
                'job_order' => \App\Models\PermintaanProduksi::count(),
            ]
        ];

        return view('auth.settings', compact('systemInfo'));
    }

    /**
     * ============================================
     * REGISTER - DAFTAR AKUN BARU
     * ============================================
     */

    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Process registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_num|unique:admins,username',
            'password' => 'required|string|min:6|max:255|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'username.required' => 'Username harus diisi',
            'username.alpha_num' => 'Username hanya boleh huruf dan angka',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        Admin::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat! Silakan login dengan akun baru Anda.');
    }

    /**
     * ============================================
     * FIRST TIME SETUP - REGISTER ADMIN PERTAMA
     * ============================================
     */
    
    /**
     * Show first time setup form
     */
    public function showFirstAdminSetup()
    {
        // Redirect ke login kalau sudah ada admin
        if (Admin::count() > 0) {
            return redirect()->route('login')->with('error', 'Setup sudah selesai. Silakan login!');
        }
        
        return view('auth.first-time-setup');
    }

    /**
     * Process first time setup
     */
    public function storeFirstAdmin(Request $request)
    {
        // Double check - kalau sudah ada admin, reject
        if (Admin::count() > 0) {
            return redirect()->route('login')->with('error', 'Setup sudah selesai!');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'username' => 'required|string|max:50|alpha_num|unique:admins,username',
            'password' => 'required|string|min:6|max:255|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Create super admin
        Admin::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin',      // Admin pertama = admin role
            'status' => 'aktif',
        ]);

        return redirect()->route('login')->with('success', 'Setup berhasil! Silakan login dengan akun yang baru dibuat.');
    }
}
