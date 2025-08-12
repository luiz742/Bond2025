<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Service;
use App\Models\InvoiceService;


class InvoiceController extends Controller
{
    public function index()
    {
        // Pega os invoices do fakeInvoices() sem modificar estrutura do "to"
        $invoices = Invoice::with('services')->paginate(15);
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

        $services = Service::all(); // Carrega todos os serviços

        $lastInvoice = Invoice::orderByDesc('id')->first();

        $nextInvoiceNumber = 1;
        if ($lastInvoice) {
            $nextInvoiceNumber = intval($lastInvoice->invoice_number) + 1;
        }

        return Inertia::render('Invoices/Create', [
            'users' => $users,
            'services' => $services,             // envia services aqui
            'nextInvoiceNumber' => $nextInvoiceNumber,
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'date' => 'nullable|date',
            'payment_due' => 'required|date',
            'currency' => 'required|string|max:10',
            'user_id' => 'required|exists:users,id',
            'client_id' => 'required_if:to_type,client|nullable|exists:clients,id',
            'to_name' => 'required|string',
            'to_address' => 'nullable|string',
            'to_type' => 'required|string',
            'type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Se to_address estiver vazio ou null, setar um valor padrão
        if (empty($data['to_address'])) {
            $data['to_address'] = '-';  // Pode ser qualquer string padrão aceitável
        }

        // Se date vazio, seta a data atual (opcional)
        if (empty($data['date'])) {
            $data['date'] = date('Y-m-d');
        }

        Invoice::create($data);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }


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
            'description' => 'required|string',
            'to_tax_registration_number' => 'nullable|string|max:255',
            'type' => 'required|string', // Adicionando o campo type
        ]);

        $data['user_id'] = $invoice->user_id;
        $data['to_type'] = $invoice->to_type; // Manter o tipo de destinatário original
        $data['type'] = $request->input('type', $invoice->to_type); // Atualizar o tipo se fornecido

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
            'type' => $invoice->type,
            'to' => [
                'name' => $invoice->to_name,
                'address' => $invoice->to_address,
                'tax_registration_number' => $invoice->to_tax_registration_number,
            ],
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

        // Nome da view baseado no type
        $component = $invoice->type === 'sheikhdom'
            ? 'Invoices/PrintableSheikhdom'
            : 'Invoices/Printable';

        return Inertia::render($component, [
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
