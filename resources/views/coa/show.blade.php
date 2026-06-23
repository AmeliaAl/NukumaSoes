<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail COA (Daftar Akun)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <strong>Kode Akun:</strong> {{ $coa->kode_akun }}
                    </div>
                    <div class="mb-4">
                        <strong>Header Akun:</strong> {{ $coa->header_akun }}
                    </div>
                    <div class="mb-4">
                        <strong>Nama Akun:</strong> {{ $coa->nama_akun }}
                    </div>
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('coa.index') }}" class="mr-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Kembali
                        </a>
                        <a href="{{ route('coa.edit', $coa) }}" class="mr-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
