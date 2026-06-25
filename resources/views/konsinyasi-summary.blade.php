<div class="mt-4 flex justify-end">
    <div class="w-full max-w-md rounded-xl border bg-white p-4 shadow-sm">
        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <span class="text-gray-600">Total Barang</span>
                <span class="font-medium">
                    Rp {{ number_format($record->total_barang ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="flex items-center justify-between">
                <span class="text-gray-600">Diskon Faktur</span>
                <span class="font-medium">
                    Rp {{ number_format($record->diskon ?? 0, 0, ',', '.') }}
                </span>
            </div>

            <div class="border-t pt-3 flex items-center justify-between text-base font-bold">
                <span>Total Piutang</span>
                <span>
                    Rp {{ number_format($record->total_piutang ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>