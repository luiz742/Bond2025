<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class FamilyMemberController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'label' => 'required|string|max:255',
        ]);

        $member = FamilyMember::create([
            'client_id' => $request->client_id,
            'name' => $request->name,
            'label' => $request->label,
        ]);

        return back()
            ->with('familyMember', $member)
            ->banner('Family member added successfully.');
    }

}
