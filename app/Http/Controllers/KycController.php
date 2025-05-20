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
        // Verifica se o usuário já enviou um KYC
        if (Kyc::where('user_id', auth()->id())->exists()) {
            return redirect()->back()->withErrors(['error' => 'Você já enviou seu KYC.']);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:b2b,b2c'],
            'company_trade_license' => $request->type === 'b2b' ? ['required', 'file'] : ['nullable'],
            'tax_certificate' => $request->type === 'b2b' ? ['required', 'file'] : ['nullable'],
            'passport_copy' => $request->type === 'b2c' ? ['required', 'file'] : ['nullable'],
            'id_proof' => $request->type === 'b2c' ? ['required', 'file'] : ['nullable'],
        ]);

        $kyc = new Kyc();
        $kyc->user_id = auth()->id();
        $kyc->type = $request->type;

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


        $kyc->save();

        return redirect()->back()->with('success', 'KYC enviado com sucesso!');
    }

}
