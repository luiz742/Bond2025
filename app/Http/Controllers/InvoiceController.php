<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\User;

class InvoiceController extends Controller
{
    private function fakeInvoices()
    {
        return [
            1 => [
                'id' => 1,
                'invoice_number' => 'INV-001',
                'date' => '2025-05-28',
                'to' => [
                    'name' => 'John Doe',
                    'address' => '123 Customer St, Dubai, UAE',
                    'tax_registration_number' => 'TRN1234567890',
                ],
                'currency' => 'AED',
                'payment_due' => '2025-06-10',
            ],
            // você pode adicionar mais faturas aqui, ex:
            2 => [
                'id' => 2,
                'invoice_number' => 'INV-002',
                'date' => '2025-06-01',
                'to' => [
                    'name' => 'Jane Smith',
                    'address' => '456 Client Rd, Abu Dhabi, UAE',
                    'tax_registration_number' => 'TRN0987654321',
                ],
                'currency' => 'AED',
                'payment_due' => '2025-06-15',
            ],
        ];
    }

    public function index()
    {
        // Pega os invoices do fakeInvoices() sem modificar estrutura do "to"
        $invoices = Invoice::paginate(15);
        // dd($invoices);
        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function create()
    {
        $users = User::with('clients')
            ->select('id', 'name')
            ->get();

        $lastInvoice = Invoice::orderByDesc('id')->first();

        $nextInvoiceNumber = 1;
        if ($lastInvoice) {
            $nextInvoiceNumber = intval($lastInvoice->invoice_number) + 1;
        }

        return Inertia::render('Invoices/Create', [
            'users' => $users,
            'nextInvoiceNumber' => $nextInvoiceNumber,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'date' => 'required|date',
            'payment_due' => 'required|date',
            'currency' => 'required|string|max:10',
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required_if:to_type,client|nullable|exists:clients,id',
            'to_name' => 'required|string',
            'to_address' => 'required|string',
            'to_type' => 'required|string', // Adicionando o campo to_type
        ]);

        Invoice::create($data);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }


    public function show($id)
    {
        $invoice = $this->fakeInvoices()[$id] ?? null;

        if (!$invoice) {
            abort(404);
        }

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function printable($id)
    {
        $invoice = Invoice::with('client') // opcional, se quiser relações
            ->find($id);

        if (!$invoice) {
            abort(404);
        }

        // Transformar dados para o formato esperado no Vue
        $formattedInvoice = [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'date' => $invoice->date,
            'payment_due' => $invoice->payment_due,
            'currency' => $invoice->currency,
            'to' => [
                'name' => $invoice->to_name,
                'address' => $invoice->to_address,
                'tax_registration_number' => $invoice->to_tax_registration_number,
            ],
        ];

        return Inertia::render('Invoices/Printable', [
            'invoice' => $formattedInvoice,
        ]);
    }

}
