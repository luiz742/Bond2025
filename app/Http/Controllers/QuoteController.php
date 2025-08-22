<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Quote;
use App\Models\User;
use App\Models\Client;
use App\Models\QuoteService;
use App\Models\Invoice;
use Illuminate\Support\Str;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with('services')->paginate(15);

        return Inertia::render('Quotes/Index', [
            'quotes' => $quotes,
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name')->get();
        $clients = Client::select('id', 'name')->get();

        $lastQuote = Quote::orderByDesc('id')->first();
        $nextQuoteNumber = $lastQuote ? intval($lastQuote->quote_number) + 1 : 1;

        return Inertia::render('Quotes/Create', [
            'users' => $users,
            'clients' => $clients,
            'nextQuoteNumber' => $nextQuoteNumber,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'quote_number' => 'required|string|max:255',
            'date' => 'nullable|date',
            'valid_until' => 'required|date',
            'currency' => 'required|string|max:10',
            'user_id' => 'required|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',
            'to_name' => 'required|string',
            'to_address' => 'nullable|string',
            'to_type' => 'required|string',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'bond_tax' => 'nullable|string|max:255',
        ]);

        if (empty($data['to_address'])) {
            $data['to_address'] = '-';
        }

        if (empty($data['date'])) {
            $data['date'] = date('Y-m-d');
        }

        // Teste: mostrar dados recebidos no log
        \Log::info('Quote store data', $data);

        Quote::create($data);

        return redirect()->route('quotes.index')->with('success', 'Quote created successfully.');
    }


    public function show($id)
    {
        $quote = Quote::with('client', 'services')->findOrFail($id);

        return Inertia::render('Quotes/Show', [
            'quote' => $quote,
            'client' => Client::find($quote->client_id),
            'user' => User::find($quote->user_id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);

        $data = $request->validate([
            'quote_number' => 'required|string|max:255',
            'date' => 'required|date',
            'valid_until' => 'required|date',
            'client_id' => 'nullable|exists:clients,id',
            'to_address' => 'nullable|string',
            'description' => 'required|string',
            'to_tax_registration_number' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'bond_tax' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = $quote->user_id;
        $data['to_type'] = $quote->to_type;

        $quote->update($data);

        return redirect()->back()->banner('Quote updated successfully.');
    }

    public function printable($id)
    {
        $quote = Quote::with('client', 'services')->findOrFail($id);

        $formattedQuote = [
            'id' => $quote->id,
            'quote_number' => $quote->quote_number,
            'date' => $quote->date,
            'valid_until' => $quote->valid_until,
            'currency' => $quote->currency,
            'type' => $quote->type,
            'bond_tax' => $quote->bond_tax,
            'to' => [
                'name' => $quote->to_name,
                'address' => $quote->to_address,
                'tax_registration_number' => $quote->to_tax_registration_number,
            ],
            'services' => $quote->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'quantity' => $service->quantity,
                    'unit_price' => $service->unit_price,
                    'total' => $service->total,
                    'total_usd' => $service->total_usd,
                ];
            }),
        ];

        $component = $quote->type === 'sheikhdom'
            ? 'Quotes/PrintableSheikhdom'
            : 'Quotes/Printable';

        return Inertia::render($component, [
            'quote' => $formattedQuote,
        ]);
    }

    public function createInvoiceFromQuote($quoteId)
    {
        $quote = Quote::findOrFail($quoteId);

        // Se já tiver chain_id, reutiliza, se não gera um novo
        $chainId = $quote->chain_id ?? Str::uuid()->toString();

        // Atualiza a quote com o chain_id, se ainda não tiver
        if (!$quote->chain_id) {
            $quote->chain_id = $chainId;
            $quote->save();
        }

        $invoiceData = $quote->transformToInvoice();

        // Adiciona o chain_id no invoice
        $invoiceData['chain_id'] = $chainId;

        // 🔹 --- Lógica de invoice_number igual ao store() ---
        $lastInvoiceNumber = Invoice::where('type', $invoiceData['type'])
            ->max('invoice_number');

        if ($lastInvoiceNumber) {
            $nextInvoiceNumber = (int) $lastInvoiceNumber + 1;
        } else {
            $nextInvoiceNumber = 1001;
        }

        $invoiceData['invoice_number'] = (string) $nextInvoiceNumber;
        // 🔹 --- fim da lógica ---

        $invoice = Invoice::create($invoiceData);

        // Se precisar copiar serviços da quote para invoice
        // foreach ($quote->services as $service) {
        //     $invoice->services()->create($service->toArray());
        // }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created from quote successfully.');
    }
}
