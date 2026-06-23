<x-app-layout>
<div class="p-4 md:p-8">
    <div class="max-w-2xl mx-auto">
        <!-- Import Header -->
        <div class="text-center mb-12">
            <div class="w-24 h-24 bg-gradient-to-b from-[#28a745] to-[#1e7e34] rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-4">
                Import Data COA
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto leading-relaxed">
                Upload file Excel (.xlsx atau .xls) dengan format kolom: <strong>kode_akun | header_akun | nama_akun</strong>
            </p>
        </div>

        <!-- Upload Card -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 p-10">
            <!-- Upload Area -->
            <form action="{{ route('coa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="border-4 border-dashed border-gray-300 rounded-3xl p-12 text-center hover:border-[#28a745] hover:bg-green-50 transition-all duration-300 cursor-pointer group hover:shadow-xl">
                    <input type="file" name="file" accept=".xlsx,.xls" required 
                           id="file-upload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10 peer"
                           onchange="updateFileName(this)">
                    
                    <div class="space-y-4">
                        <svg class="w-20 h-20 mx-auto text-gray-400 group-hover:text-[#28a745] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4m-7 5v3a1 1 0 001 1h6a1 1 0 001-1v-3" />
                        </svg>
                        
                        <div>
                            <p class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-[#28a745]">
                                Klik untuk upload file Excel
                            </p>
                            <p class="text-lg text-gray-500">
                                atau drag & drop (maks 2MB)
                            </p>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center pt-4 border-t border-gray-200">
                            <span id="file-name" class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-xl font-medium hidden">
                                Tidak ada file dipilih
                            </span>
                            <span class="text-xs text-gray-400">.xlsx atau .xls</span>
                        </div>
                    </div>
                </div>

                @error('file')
                    <div class="p-4 bg-red-50 border-2 border-red-200 rounded-2xl text-red-700 text-sm">
                        {{ $message }}
                    </div>
                @enderror

                @if (session('error'))
                    <div class="p-4 bg-red-50 border-2 border-red-200 rounded-2xl text-red-700 text-sm animate-pulse">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Buttons -->
                <div class="flex gap-4 pt-8 border-t border-gray-200">
                    <button type="submit" 
                            class="flex-1 group relative px-8 py-4 font-bold text-white transition-all duration-300 bg-gradient-to-b from-[#28a745] to-[#1e7e34] rounded-2xl shadow-[0_8px_0_0_#155724] hover:shadow-[0_4px_0_0_#155724] hover:translate-y-[4px] active:shadow-none active:translate-y-[8px] disabled:opacity-50">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Import Sekarang
                        </span>
                    </button>
                    
                    <a href="{{ route('coa.index') }}" 
                       class="px-8 py-4 font-bold text-gray-700 bg-white border-2 border-gray-300 rounded-2xl shadow-sm hover:bg-gray-50 hover:border-gray-400 hover:shadow-md transition-all flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </a>
                </div>
            </form>

            <!-- Format Guide -->
            <div class="mt-12 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-100">
                <h3 class="font-bold text-lg text-blue-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Format Excel
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="bg-white p-4 rounded-xl shadow-sm border">
                        <strong>Kolom A:</strong> <code>kode_akun</code><br>
                        Contoh: <code>111</code>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border">
                        <strong>Kolom B:</strong> <code>header_akun</code><br>
                        Contoh: <code>1</code>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow-sm border">
                        <strong>Kolom C:</strong> <code>nama_akun</code><br>
                        Contoh: <code>Kas</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = document.getElementById('file-name');
            const label = input.closest('.border-4').querySelector('p.text-2xl');
            
            if (input.files.length > 0) {
                const file = input.files[0];
                const name = file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name;
                fileName.textContent = name;
                fileName.classList.remove('hidden');
                label.innerHTML = 'File terpilih: <strong>' + name + '</strong>';
                label.classList.add('text-[#28a745]');
            }
        }
    </script>
</x-app-layout>
