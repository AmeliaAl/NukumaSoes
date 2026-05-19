<x-guest-layout>
    <div>
        <div class="w-full max-w-md bg-gray-500/90 rounded-2xl shadow-xl p-8 text-white">

            <h1 class="text-4xl font-serif text-center mb-10">
                Sign Up
            </h1>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label
                        for="name"
                        value="Name"
                        class="text-lg font-serif text-white mb-2" />

                    <x-text-input
                        id="name"
                        type="text"
                        name="name"
                        :value="old('name')"
                        required
                        autofocus
                        class="w-full rounded-full px-5 py-3 bg-gray-200 text-gray-800 focus:outline-none" />

                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label
                        for="email"
                        value="Email"
                        class="text-lg font-serif text-white mb-2" />

                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        class="w-full rounded-full px-5 py-3 bg-gray-200 text-gray-800 focus:outline-none" />

                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label
                        for="password"
                        value="Password"
                        class="text-lg font-serif text-white mb-2" />

                    <x-text-input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-full px-5 py-3 bg-gray-200 text-gray-800 focus:outline-none" />

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>


                <!-- Confirm Password -->
                <div>
                    <x-input-label
                        for="password_confirmation"
                        value="Confirm Password"
                        class="text-lg font-serif text-white mb-2" />

                    <x-text-input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        class="w-full rounded-full px-5 py-3 bg-gray-200 text-gray-800 focus:outline-none" />

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Role -->
                <div>
                    <x-input-label
                        for="role"
                        value="Role"
                        class="text-lg font-serif text-white mb-2" />

                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full rounded-full px-5 py-3 bg-gray-200 text-gray-800 focus:outline-none">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="pemilik">Pemilik</option>
                    </select>

                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Button -->
                <div class="flex justify-center">
                    <button
                        type="submit"
                        class="mt-6 px-10 py-3 rounded-full border border-white text-white hover:bg-white hover:text-gray-700 transition">
                        Sign Up
                    </button>
                </div>

                <!-- Login Link -->
                <p class="text-center mt-6 text-sm">
                    Already have an account?
                    <a href="{{ route('login') }}" class="underline font-semibold">
                        Sign In
                    </a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
