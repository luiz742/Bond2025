<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuoteService;
use App\Models\Quote;

class QuoteServiceController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'quote_id' => 'required|exists:quotes,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'currency' => 'nullable|string|max:10',
            'auto_conversion' => 'nullable|boolean',
            'total_usd' => 'nullable|numeric|min:0',
        ]);

        $data['quantity'] = $data['quantity'] ?? 1;
        $data['total'] = $data['quantity'] * $data['unit_price'];

        $quote = Quote::findOrFail($data['quote_id']);
        $currency = $quote->currency ?? 'USD';
        $data['currency'] = $currency;

        $autoConversion = $request['auto_conversion'];

        if (!$autoConversion) {
            // Usa valor manual: total_usd é a cotação da moeda (ex: AED = 3.65)
            $rate = $data['total_usd'] ?? 1; // se não enviou, assume 1

            $data['total_usd'] = round($data['total'] * $rate, 2);
        } else {
            // Calcula automaticamente
            $rate = QuoteService::getExchangeRate($currency);
            $data['total_usd'] = round($data['total'] * $rate, 2);
        }

        QuoteService::create($data);

        return redirect()->back()->with('success', 'Service added successfully.');
    }


    public function update(Request $request, $id)
    {
        $service = QuoteService::findOrFail($id);

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
