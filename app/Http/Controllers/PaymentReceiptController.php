<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('services')
            ->where('paid', true)
            ->orderByDesc('paid_at')
            ->paginate(10)
            ->withQueryString();

        return inertia('PaymentReceipt/Index', [
            'invoices' => $invoices,
        ]);
    }

    /**
     * Exibir recibo em tela
     */
    public function show(Invoice $invoice)
    {
        if (!$invoice->paid) {
            abort(404, 'Recibo não disponível para faturas não pagas.');
        }

        return inertia('Invoices/Receipt', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * Baixar recibo em PDF (opcional)
     */
    public function download(Invoice $invoice)
    {
        if (!$invoice->paid) {
            abort(404, 'Recibo não disponível para faturas não pagas.');
        }

        // Aqui você pode usar dompdf / snappy etc
        $pdf = \PDF::loadView('pdf.receipt', ['invoice' => $invoice]);
        return $pdf->download("receipt-{$invoice->id}.pdf");
    }
}
