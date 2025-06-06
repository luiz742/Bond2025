<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Document;

class DocumentController extends Controller
{
    // Lista os documentos de um serviço via Inertia (pode ser usado na Show do service)
    public function index(Service $service)
    {
        $documents = $service->documents()->latest()->get();

        return inertia('Admin/Documents/Index', [
            'service' => $service,
            'documents' => $documents,
        ]);
    }

    // Não precisaremos de create separado, usaremos modal na Show do Service

    // Salva um novo documento via Inertia
    public function store(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:client,company',
            'client_type' => 'required|string|in:main,spouse,child_1,child_2,child_3,child_4', // ajuste conforme os tipos que quiser permitir
        ]);

        $service->documents()->create([
            'name' => $request->name,
            'type' => $request->type,
            'client_type' => $request->client_type,
        ]);

        // Retorna para a mesma página Service Show (via Inertia)
        return redirect()->route('admin.services.show', $service)
            ->banner('Document added successfully.');
    }

    // Formulário de edição pode ser via modal ou página separada (a definir)

    // Atualiza documento
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:client,company',
        ]);

        $document->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return redirect()->route('admin.services.show', $document->service_id)
            ->banner('Document updated successfully.');
    }

    // Deleta documento
    public function destroy(Document $document)
    {
        $serviceId = $document->service_id;
        $document->delete();

        return redirect()->route('admin.services.show', $serviceId)
            ->banner('Document deleted successfully.');
    }
}
