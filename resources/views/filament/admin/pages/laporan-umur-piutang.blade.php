<x-filament-panels::page>
    @php $data = $this->getData(); @endphp

    <style>
        .aging-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; background-color: #ffffff; }
        .aging-table th, .aging-table td { border: 1px solid #d1d5db; padding: 8px 12px; background-color: #ffffff; }
        .aging-table thead tr th { background-color: #f9fafb !important; }
        .aging-table th { font-weight: 600; color: #374151; }
        .aging-table .row-group td { background-color: #eff6ff !important; font-weight: 600; color: #1d4ed8; }
        .aging-table .row-subtotal td { background-color: #f3f4f6 !important; font-weight: 600; }
        .aging-table .row-grand td { background-color: #1f2937 !important; color: white; font-weight: 700; border-color: #4b5563; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>

    <div class="space-y-4">
        <p class="text-sm text-gray-500">Per tanggal: {{ $data['tanggal'] }}</p>

        <div class="overflow-x-auto rounded-xl">
            <table class="aging-table">
                <thead>
                    <tr>
                        <th class="text-left">Nama</th>
                        <th class="text-left">Invoice / Tagihan</th>
                        <th class="text-right">Belum Jatuh Tempo</th>
                        <th class="text-right">1–30 Hari</th>
                        <th class="text-right">31–60 Hari</th>
                        <th class="text-right">61–90 Hari</th>
                        <th class="text-right">&gt;90 Hari</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['grouped'] as $group)
                        <tr class="row-group">
                            <td colspan="2">{{ $group['nama'] }}</td>
                            <td colspan="6"></td>
                        </tr>

                        @foreach ($group['items'] as $item)
                            <tr>
                                <td></td>
                                <td>{{ $item['referensi'] }}</td>
                                <td class="text-right">{{ $item['belum_jatuh'] > 0 ? 'Rp ' . number_format($item['belum_jatuh'], 0, ',', '.') : '-' }}</td>
                                <td class="text-right">{{ $item['hari_1_30'] > 0 ? 'Rp ' . number_format($item['hari_1_30'], 0, ',', '.') : '-' }}</td>
                                <td class="text-right">{{ $item['hari_31_60'] > 0 ? 'Rp ' . number_format($item['hari_31_60'], 0, ',', '.') : '-' }}</td>
                                <td class="text-right">{{ $item['hari_61_90'] > 0 ? 'Rp ' . number_format($item['hari_61_90'], 0, ',', '.') : '-' }}</td>
                                <td class="text-right">{{ $item['hari_90plus'] > 0 ? 'Rp ' . number_format($item['hari_90plus'], 0, ',', '.') : '-' }}</td>
                                <td class="text-right"><strong>Rp {{ number_format($item['total'], 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach

                        <tr class="row-subtotal">
                            <td colspan="2">Subtotal {{ $group['nama'] }}</td>
                            <td class="text-right">{{ $group['subtotal']['belum_jatuh'] > 0 ? 'Rp ' . number_format($group['subtotal']['belum_jatuh'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $group['subtotal']['hari_1_30'] > 0 ? 'Rp ' . number_format($group['subtotal']['hari_1_30'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $group['subtotal']['hari_31_60'] > 0 ? 'Rp ' . number_format($group['subtotal']['hari_31_60'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $group['subtotal']['hari_61_90'] > 0 ? 'Rp ' . number_format($group['subtotal']['hari_61_90'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $group['subtotal']['hari_90plus'] > 0 ? 'Rp ' . number_format($group['subtotal']['hari_90plus'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">Rp {{ number_format($group['subtotal']['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding: 2rem; color: #9ca3af;">
                                Tidak ada piutang outstanding.
                            </td>
                        </tr>
                    @endforelse

                    @if (!empty($data['grouped']))
                        <tr class="row-grand">
                            <td colspan="2">GRAND TOTAL</td>
                            <td class="text-right">{{ $data['grandTotal']['belum_jatuh'] > 0 ? 'Rp ' . number_format($data['grandTotal']['belum_jatuh'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $data['grandTotal']['hari_1_30'] > 0 ? 'Rp ' . number_format($data['grandTotal']['hari_1_30'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $data['grandTotal']['hari_31_60'] > 0 ? 'Rp ' . number_format($data['grandTotal']['hari_31_60'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $data['grandTotal']['hari_61_90'] > 0 ? 'Rp ' . number_format($data['grandTotal']['hari_61_90'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">{{ $data['grandTotal']['hari_90plus'] > 0 ? 'Rp ' . number_format($data['grandTotal']['hari_90plus'], 0, ',', '.') : '-' }}</td>
                            <td class="text-right">Rp {{ number_format($data['grandTotal']['total'], 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
