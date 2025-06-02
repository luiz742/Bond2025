<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PdfDownloadController extends Controller
{
    public function index()
    {
        // Supondo que os arquivos estejam em storage/app/public/pdfs
        $files = Storage::disk('public')->files('pdfs');

        // Formata os arquivos para o front
        $pdfFiles = collect($files)->map(function ($file) {
            return [
                'name' => basename($file),
                'url' => Storage::url($file),
            ];
        });

        return Inertia::render('PdfDownloads/Index', [
            'pdfFiles' => $pdfFiles,
        ]);
    }
}
