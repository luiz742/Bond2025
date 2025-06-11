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

    public function reupload(Request $request, $id)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'Arquivo não enviado'], 422);
        }

        $uploadedFile = $request->file('file');

        // Exemplo: salvar em storage/app/reuploads/
        $path = $uploadedFile->store('reuploads');

        // Aqui você pode atualizar o modelo File no banco:
        $file = File::findOrFail($id);
        $file->path = $path;
        $file->status = 'pending'; // Ou outro status, como 'reuploaded'
        $file->rejection_reason = null;
        $file->save();

        return back()->withErrors(['file' => 'Arquivo não enviado']);
    }

    public function update(Request $request, Client $client, $id)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
        ]);

        $file = File::findOrFail($id);

        // dd($file);
        // dd($request->all());

        // Exclui o arquivo antigo, se existir
        if ($file->path && Storage::exists($file->path)) {
            Storage::delete($file->path);
        }

        // Salva o novo arquivo
        $newPath = $request->file('file')->store("clients/{$client->id}/documents");

        // Atualiza o modelo File
        $file->update([
            'path' => $newPath,
            'status' => 'pending', // ou outro valor padrão
        ]);

        return back()->banner('Documento atualizado com sucesso.');
    }
}
