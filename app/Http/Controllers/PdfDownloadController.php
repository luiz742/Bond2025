<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PdfDownloadController extends Controller
{
    public function index()
    {
        // Acessa arquivos diretamente em public/images
        $files = File::files(public_path('images'));

        // Mapeia os arquivos para o frontend
        $pdfFiles = collect($files)->map(function ($file) {
            return [
                'name' => $file->getFilename(),
                'url' => asset('images/' . $file->getFilename()), // gera URL pública
            ];
        });

        return Inertia::render('PdfDownloads/Index', [
            'pdfFiles' => $pdfFiles,
        ]);
    }
}
