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
        ]);

        $data['quantity'] = $data['quantity'] ?? 1;
        $data['total'] = $data['quantity'] * $data['unit_price'];

        $quote = Quote::findOrFail($data['quote_id']);
        $currency = $quote->currency ?? 'USD';
        $data['currency'] = $currency;

        $rate = QuoteService::getExchangeRate($currency);
        $data['total_usd'] = round($data['total'] * $rate, 2);

        $service = QuoteService::create($data);

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
