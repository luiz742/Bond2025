<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::latest()->get();

        return inertia('Admin/Services/Index', [
            'services' => $services
        ]);
    }

    public function create()
    {
        return inertia('Admin/Services/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        Service::create($request->only('name'));

        return redirect()->route('admin.services.index')
            ->with('success', 'Service has been created.');
    }

    public function show($id)
    {
        $service = Service::with('documents')->findOrFail($id);
        $documents = Document::where('service_id', $id)->get();
        return inertia('Admin/Services/Show', [
            'service' => $service,
            'documents' => $documents
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            // adicione outras validações se necessário
        ]);

        $service = Service::findOrFail($id);
        $service->update([
            'name' => $request->name,
            'country' => $request->country,
            // adicione outros campos se necessário
        ]);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service has been updated.');
    }

}
