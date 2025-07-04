<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\User;
use App\Models\InvoiceService;


class InvoiceController extends Controller
{
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


    // public function show($id)
    // {
    //     $invoice = Invoice::with('client', 'services') // opcional, se quiser relações
    //         ->find($id);

    //     if (!$invoice) {
    //         abort(404);
    //     }

    //     $formattedInvoice = [
    //         'id' => $invoice->id,
    //         'invoice_number' => $invoice->invoice_number,
    //         'date' => $invoice->date,
    //         'payment_due' => $invoice->payment_due,
    //         'currency' => $invoice->currency,
    //         'description' => $invoice->description,
    //         'to_name' => $invoice->to_name,
    //         'to_address' => $invoice->to_address,
    //         'to_tax_registration_number' => $invoice->to_tax_registration_number,
    //     ];

    //     return Inertia::render('Invoices/Show', [
    //         'invoice' => $formattedInvoice,
    //     ]);
    // }

    public function show($id)
    {
        $invoice = Invoice::with('client', 'services')->findOrFail($id);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'date' => 'required|date',
            'payment_due' => 'required|date',
            'client_id' => 'required_if:to_type,client|nullable|exists:clients,id',
            'to_address' => 'required|string',
            'description' => 'nullable|string',
            'to_tax_registration_number' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = $invoice->user_id;
        $data['to_type'] = $invoice->to_type; // Manter o tipo de destinatário original


        $invoice->update($data);

        return redirect()->back()->banner('Invoice updated successfully.');
    }

    public function printable($id)
    {
        $invoice = Invoice::with('client', 'services')->find($id);

        if (!$invoice) {
            abort(404);
        }

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
            // Adiciona os serviços formatados
            'services' => $invoice->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'quantity' => $service->quantity,
                    'unit_price' => $service->unit_price,
                    'total' => $service->total,
                ];
            }),
        ];

        return Inertia::render('Invoices/Printable', [
            'invoice' => $formattedInvoice,
        ]);
    }

    public function storeInvoiceService(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
        ]);

        // Define quantity fixo em 1
        $data['quantity'] = 1;

        // Calcula total igual ao unit_price
        $data['total'] = $data['unit_price'];

        $service = InvoiceService::create($data);

        return redirect()->back()->with('success', 'Service added successfully.');
    }


    public function updateInvoiceService(Request $request, $id)
    {
        $service = InvoiceService::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        $data['total'] = $data['quantity'] * $data['unit_price'];

        $service->update($data);

        return redirect()->back()->with('success', 'Service updated successfully.');
    }

}
