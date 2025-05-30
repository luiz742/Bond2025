<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Client;
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

        $client = Client::findOrFail($request->client_id);

        foreach ($request->file('files') as $documentId => $file) {
            $path = $file->store("uploads/clients/{$request->client_id}", 'public');

            File::create([
                'client_id' => $request->client_id,
                'document_id' => $documentId,
                'path' => $path,
                'status' => 'pending',
            ]);

            // ✅ Cria uma notificação por arquivo enviado
            $client->notifyDocumentUploaded($documentId);
        }

        return back()->banner('Files have been uploaded.');
    }

}
