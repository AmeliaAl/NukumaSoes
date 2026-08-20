<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah COA (Daftar Akun)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('coa.store') }}">
                        @csrf

                        <!-- Manual Fields (Optional) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <x-input-label for="kode_akun" :value="__('Kode Akun')" />
                                <x-text-input id="kode_akun" class="block mt-1 w-full" type="text" name="kode_akun" :value="old('kode_akun')" autocomplete="kode_akun" />
                                <x-input-error :messages="$errors->get('kode_akun')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nama_akun" :value="__('Nama Akun')" />
                                <x-text-input id="nama_akun" class="block mt-1 w-full" type="text" name="nama_akun" :value="old('nama_akun')" autocomplete="nama_akun" />
                                <x-input-error :messages="$errors->get('nama_akun')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 mb-8">
                            <a href="{{ route('coa.index') }}" class="mr-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Manual') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <hr class="my-8 border-gray-200">

                    <!-- Excel Import Section -->
                    <div class="mb-6 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border-2 border-dashed border-blue-200">
                        <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Import dari Excel (Alternatif)
                        </h3>
                        <form action="{{ route('coa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
                                <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">Format: kode_akun | nama_akun</p>
                            </div>
                            <div class="flex flex-col gap-3">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input type="checkbox" name="hapus_lama" value="1" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    Hapus COA lama sebelum import
                                </label>
                                <p class="text-xs text-gray-500">Centang jika Anda ingin mengganti semua daftar COA saat ini dengan data baru dari file Excel.</p>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all">
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12a2 2 0 01-2 2H9a2 2 0 01-2-2v-12a2 2 0 012-2h8a2 2 0 012 2z"/>
                                    </svg>
                                    Import Excel
                                </button>
                                <a href="{{ route('coa.index') }}" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-xl shadow-lg transition-all">
                                    Kembali
                                </a>
                            </div>
                        </form>
                        @error('file')
                            <p class="mt-2 text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                        @if (session('success'))
                            <div class="mt-4 p-4 bg-green-100 border border-green-400 rounded-xl text-green-700">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="mt-4 p-4 bg-red-100 border border-red-400 rounded-xl text-red-700">
                                {!! session('error') !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
