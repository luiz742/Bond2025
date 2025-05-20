<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'files' => 'required|array',
            'files.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        foreach ($request->file('files') as $documentId => $file) {
            $path = $file->store("uploads/clients/{$request->client_id}", 'public');

            File::create([
                'client_id' => $request->client_id,
                'document_id' => $documentId,
                'path' => $path,
                'status' => 'pending',
            ]);
        }

        return back()->with('success', 'Arquivos enviados com sucesso.');
    }


}
