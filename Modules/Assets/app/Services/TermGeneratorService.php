<?php

namespace Modules\Assets\App\Services;

use App\Services\PdfService;
use Illuminate\Support\Str;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetResponsibilityTerm;

class TermGeneratorService
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Generate a professional responsibility term PDF.
     */
    public function generateTerm(Asset $asset, $user, string $type): AssetResponsibilityTerm
    {
        $data = [
            'asset' => $asset,
            'user' => $user,
            'type' => $type,
            'date' => now()->format('d/m/Y'),
        ];

        $html = view('assets::admin.terms.pdf-template', $data)->render();
        $fileName = 'terms/' . $asset->code . '_' . Str::random(8) . '.pdf';

        $this->pdfService->saveToStorage($html, $fileName, 'public', 'A4', 'Portrait', [20, 15, 20, 15]);

        return AssetResponsibilityTerm::create([
            'asset_id' => $asset->id,
            'user_id' => $user->id,
            'type' => $type,
            'generated_at' => now(),
            'file_path' => $fileName,
        ]);
    }
}
