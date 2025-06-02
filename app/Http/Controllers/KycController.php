<?php

// app/Http/Controllers/KycController.php

namespace App\Http\Controllers;

use App\Models\Kyc;
use Illuminate\Http\Request;

class KycController extends Controller
{
    // Listar todos os KYC do usuário logado
    public function index()
    {
        $kycs = Kyc::where('user_id', auth()->id())->get();

        return inertia('Kyc/Index', [
            'kycs' => $kycs
        ]);
    }

    public function create()
    {
        return inertia('Kyc/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:b2b,b2c'],
            'company_trade_license' => ['nullable', 'file'],
            'tax_certificate' => ['nullable', 'file'],
            'passport_copy' => ['nullable', 'file'],
            'id_proof' => ['nullable', 'file'],
            'proof_of_address' => ['nullable', 'file'],
            'company_address' => ['nullable', 'string'],
        ]);

        // Recupera ou cria novo KYC
        $kyc = Kyc::firstOrCreate(
            ['user_id' => auth()->id()],
            ['type' => $request->type]
        );

        // Atualiza os campos enviados
        if ($request->hasFile('company_trade_license')) {
            $kyc->company_trade_license = $request->file('company_trade_license')->store('kyc', 'public');
        }

        if ($request->hasFile('tax_certificate')) {
            $kyc->tax_certificate = $request->file('tax_certificate')->store('kyc', 'public');
        }

        if ($request->hasFile('passport_copy')) {
            $kyc->passport_copy = $request->file('passport_copy')->store('kyc', 'public');
        }

        if ($request->hasFile('id_proof')) {
            $kyc->id_proof = $request->file('id_proof')->store('kyc', 'public');
        }

        if ($request->hasFile('proof_of_address')) {
            $kyc->proof_of_address = $request->file('proof_of_address')->store('kyc', 'public');
        }

        if ($request->hasFile('company_utility_bill')) {
            $kyc->company_utility_bill = $request->file('company_utility_bill')->store('kyc', 'public');
        }

        if ($request->filled('company_address')) {
            $kyc->company_address = $request->company_address;
        }

        $kyc->save();

        return redirect()->back()->banner('Document sent successfully!');
    }

}
