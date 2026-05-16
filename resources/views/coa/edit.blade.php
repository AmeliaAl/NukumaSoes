<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit COA (Daftar Akun)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('coa.update', $coa) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="kode_akun" :value="__('Kode Akun')" />
                            <x-text-input id="kode_akun" class="block mt-1 w-full" type="text" name="kode_akun" :value="old('kode_akun', $coa->kode_akun)" required autofocus autocomplete="kode_akun" />
                            <x-input-error :messages="$errors->get('kode_akun')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="header_akun" :value="__('Header Akun')" />
                            <select id="header_akun" name="header_akun" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Pilih Header Akun</option>
                                <option value="Aset" {{ old('header_akun', $coa->header_akun) == 'Aset' ? 'selected' : '' }}>Aset</option>
                                <option value="Liabilitas" {{ old('header_akun', $coa->header_akun) == 'Liabilitas' ? 'selected' : '' }}>Liabilitas</option>
                                <option value="Ekuitas" {{ old('header_akun', $coa->header_akun) == 'Ekuitas' ? 'selected' : '' }}>Ekuitas</option>
                                <option value="Pendapatan" {{ old('header_akun', $coa->header_akun) == 'Pendapatan' ? 'selected' : '' }}>Pendapatan</option>
                                <option value="Beban" {{ old('header_akun', $coa->header_akun) == 'Beban' ? 'selected' : '' }}>Beban</option>
                            </select>
                            <x-input-error :messages="$errors->get('header_akun')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="nama_akun" :value="__('Nama Akun')" />
                            <x-text-input id="nama_akun" class="block mt-1 w-full" type="text" name="nama_akun" :value="old('nama_akun', $coa->nama_akun)" required autocomplete="nama_akun" />
                            <x-input-error :messages="$errors->get('nama_akun')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('coa.index') }}" class="mr-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
