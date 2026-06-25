<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SalesOrderPdfController extends Controller
{
    public function download($id)
    {
        $salesOrder = SalesOrder::with([
            'detailSalesOrder.barang',
            'penjualanNonKonsinyasi.pelanggan',
            'penjualanKonsinyasi.mitra'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.sales-order', [
            'salesOrder' => $salesOrder
        ]);

        return $pdf->stream('sales-order-' . $salesOrder->no_so . '.pdf');
    }
}
