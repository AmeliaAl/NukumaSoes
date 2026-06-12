<x-guest-layout>
    <div class="grid grid-cols-1 md:grid-cols-2 min-h-[700px]">
        
        <!-- Left Column: Branding & Background -->
        <div class="relative hidden md:flex flex-col justify-between p-16 text-white bg-[url('/images/background_fluid.png')] bg-cover bg-center overflow-hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-red-900/40 backdrop-blur-[2px]"></div>
            
            <!-- Content -->
            <div class="relative z-10">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-16 h-16 object-contain bg-white/10 rounded-full p-3 backdrop-blur-sm">
                    <span class="font-serif text-3xl font-bold tracking-wide">NUKUMA CANTIQUE</span>
                </div>
            </div>

            <div class="relative z-10 mb-16">
                <h2 class="text-7xl font-serif font-bold leading-tight mb-8">
                    NIKMATI<br>KELEZATAN<br>SOES PREMIUM
                </h2>
                <p class="text-2xl text-red-100/90 font-light max-w-md leading-relaxed">
                    Masuk untuk mengakses dasbor Anda, kelola produk, dan pantau perkembangan bisnis Anda dengan elegan.
                </p>
            </div>


        </div>

        <!-- Right Column: Login Form -->
        <div class="flex flex-col justify-center p-10 sm:p-16 md:p-20 bg-white dark:bg-gray-800">
            <div class="w-full max-w-lg mx-auto">
                <div class="mb-12 text-center md:text-left">
                     <!-- Mobile Logo (Visible only on small screens) -->
                    <div class="md:hidden flex justify-center mb-8">
                         <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-24 h-auto">
                    </div>

                    <h1 class="text-5xl font-bold text-gray-900 dark:text-white font-serif mb-3">
                        Selamat Datang Kembali!
                    </h1>
                    <p class="text-gray-500 text-xl">Silakan masukkan detail Anda untuk masuk.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-8">
                    @csrf

                    <!-- Email -->
                    <div class="relative z-0 w-full mb-8 group">
                        <input type="email" name="email" id="email" 
                            class="block py-4 px-0 w-full text-2xl text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-800 peer" 
                            placeholder=" " required autofocus />
                        <label for="email" 
                            class="peer-focus:font-medium absolute text-xl text-gray-500 duration-300 transform -translate-y-8 scale-75 top-4 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-800 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-8">
                            Alamat Email
                        </label>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 font-medium text-sm" />
                    </div>

                    <!-- Password -->
                    <div class="relative z-0 w-full mb-8 group">
                        <input type="password" name="password" id="password" 
                            class="block py-4 px-0 w-full text-2xl text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-red-800 peer" 
                            placeholder=" " required />
                        <label for="password" 
                            class="peer-focus:font-medium absolute text-xl text-gray-500 duration-300 transform -translate-y-8 scale-75 top-4 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-red-800 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-8">
                            Kata Sandi
                        </label>
                         <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 font-medium text-sm" />
                    </div>



                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between text-lg mt-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="remember" class="w-6 h-6 rounded border-gray-300 text-red-600 focus:ring-red-200 transition">
                            </div>
                            <span class="text-gray-600 group-hover:text-red-700 transition">Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-red-800 font-bold hover:text-red-900 transition underline decoration-2 underline-offset-4">
                            Lupa Kata Sandi?
                        </a>
                        @endif
                    </div>

                    <!-- Button -->
                    <button
                        type="submit"
                        class="w-full mt-10 bg-gray-900 text-white font-bold text-2xl py-5 px-6 rounded-2xl shadow-xl hover:shadow-2xl hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all duration-300 transform active:scale-[0.98]">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>