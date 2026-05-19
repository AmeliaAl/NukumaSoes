<x-filament-panels::page>
            {{-- HEADER --}}
        <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
            <div class="font-bold text-xl uppercase">Laporan Posisi Keuangan</div>
            <div class="font-semibold text-lg">NUKUMA SOES</div>
            <div class="text-sm text-gray-600">Periode {{ now()->translatedFormat('F Y') }}</div>
        </div>

        {{-- CONTAINER 2 KOLOM --}}
        <div class="border-2 border-gray-800">
            
            {{-- HEADER KOLOM --}}
            <div class="grid grid-cols-2 divide-x-2 divide-gray-800 bg-blue-600 text-white">
                <div class="font-bold px-4 py-2 text-center uppercase">AKTIVA</div>
                <div class="font-bold px-4 py-2 text-center uppercase">PASIVA</div>
            </div>

            {{-- ISI 2 KOLOM --}}
            <div class="grid grid-cols-2 divide-x-2 divide-gray-800 min-h-[400px]">
                
                {{-- ================= KOLOM KIRI: AKTIVA ================= --}}
                <div>
                    {{-- Aktiva Lancar --}}
                    <div class="bg-blue-500 text-white font-semibold px-4 py-1.5 border-b border-gray-400">
                        Aktiva Lancar
                    </div>

                    @forelse($aktivaLancar as $akun)
                        <div class="flex justify-between px-4 py-1.5 border-b border-gray-300">
                            <span class="text-gray-700">{{ $akun->nama_akun }}</span>
                            <span class="font-semibold text-right whitespace-nowrap">Rp{{ number_format($akun->saldo, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="px-4 py-1.5 text-gray-500 border-b border-gray-300">-</div>
                    @endforelse

                    <div class="flex justify-between px-4 py-1.5 font-bold bg-blue-100 border-b-2 border-gray-400">
                        <span class="uppercase">Total</span>
                        <span class="text-right whitespace-nowrap">Rp{{ number_format($totalAktivaLancar, 0, ',', '.') }}</span>
                    </div>

                    {{-- Aktiva Tetap --}}
                    <div class="bg-blue-500 text-white font-semibold px-4 py-1.5 border-b border-gray-400">
                        Aktiva Tetap
                    </div>

                    @forelse($aktivaTetap as $akun)
                        <div class="flex justify-between px-4 py-1.5 border-b border-gray-300">
                            <span class="text-gray-700">{{ $akun->nama_akun }}</span>
                            <span class="font-semibold text-right whitespace-nowrap">Rp{{ number_format($akun->saldo, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="px-4 py-1.5 text-gray-500 border-b border-gray-300">-</div>
                    @endforelse

                    <div class="flex justify-between px-4 py-1.5 font-bold bg-blue-100 border-b-2 border-gray-400">
                        <span class="uppercase">Total</span>
                        <span class="text-right whitespace-nowrap">Rp{{ number_format($totalAktivaTetap, 0, ',', '.') }}</span>
                    </div>

                    {{-- TOTAL AKTIVA --}}
                    <div class="flex justify-between px-4 py-2.5 font-bold bg-blue-600 text-white border-t-2 border-gray-800">
                        <span class="uppercase">Total Aktiva</span>
                        <span class="text-right whitespace-nowrap">Rp{{ number_format($totalAktiva, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- ================= KOLOM KANAN: PASIVA ================= --}}
                <div>
                    {{-- Kewajiban --}}
                    <div class="bg-blue-500 text-white font-semibold px-4 py-1.5 border-b border-gray-400">
                        Kewajiban
                    </div>

                    @forelse($liabilitas as $akun)
                        <div class="flex justify-between px-4 py-1.5 border-b border-gray-300">
                            <span class="text-gray-700">{{ $akun->nama_akun }}</span>
                            <span class="font-semibold text-right whitespace-nowrap">Rp{{ number_format($akun->saldo, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="px-4 py-1.5 text-gray-500 border-b border-gray-300">-</div>
                    @endforelse

                    <div class="flex justify-between px-4 py-1.5 font-bold bg-blue-100 border-b-2 border-gray-400">
                        <span class="uppercase">Total</span>
                        <span class="text-right whitespace-nowrap">Rp{{ number_format($totalLiabilitas, 0, ',', '.') }}</span>
                    </div>

                    {{-- Ekuitas --}}
                    <div class="bg-blue-500 text-white font-semibold px-4 py-1.5 border-b border-gray-400">
                        Ekuitas
                    </div>

                    @forelse($ekuitas as $akun)
                        <div class="flex justify-between px-4 py-1.5 border-b border-gray-300">
                            <span class="text-gray-700">{{ $akun->nama_akun }}</span>
                            <span class="font-semibold text-right whitespace-nowrap">Rp{{ number_format($akun->saldo, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="px-4 py-1.5 text-gray-500 border-b border-gray-300">-</div>
                    @endforelse

                    <div class="flex justify-between px-4 py-1.5 font-bold bg-blue-100 border-b-2 border-gray-400">
                        <span class="uppercase">Total</span>
                        <span class="text-right whitespace-nowrap">Rp{{ number_format($totalEkuitas, 0, ',', '.') }}</span>
                    </div>

                    {{-- TOTAL PASIVA --}}
                    <div class="flex justify-between px-4 py-2.5 font-bold bg-blue-600 text-white border-t-2 border-gray-800">
                        <span class="uppercase">Total Pasiva</span>
                        <span class="text-right whitespace-nowrap">Rp{{ number_format($totalPasiva, 0, ',', '.') }}</span>
                    </div>
                </div>

            </div>
        </div>

<x-filament-panels::page>