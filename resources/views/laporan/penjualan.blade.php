<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6 text-center">
                        <h2 class="text-xl font-bold">Soes Nukuma</h2>
                        <h3 class="text-lg font-semibold">Neraca Saldo</h3>
                    </div>

                    <div class="mb-4">
                        <form method="GET" action="{{ route('laporan.penjualan') }}" class="flex items-center justify-center">
                            <label for="periode" class="mr-2">Periode:</label>
                            <input type="text" id="periode" name="periode" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mr-2" placeholder="Masukkan periode">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
                        </form>
                    </div>

                    <table class="min-w-full bg-white">
                        <thead class="bg-orange-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-300">Nomor Akun</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-300">Nama Akun</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-300">Debit</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-black uppercase tracking-wider border-r border-gray-300">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">1101</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">Kas</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">10,000,000</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">-</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">2101</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">Hutang</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">-</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-black border-r border-gray-300">5,000,000</td>
                            </tr>
                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
