<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

/**
 * Centralized PDF Service for the entire application.
 * Uses html2pdf.app (cloud) as primary generator with mPDF as fallback.
 *
 * This service ensures high-quality, professional PDFs across all modules.
 */
class PdfService
{
    protected ?string $apiKey;
    protected bool $cloudEnabled;

    public function __construct()
    {
        $this->apiKey = config('services.html2pdf.api_key');
        $this->cloudEnabled = !empty($this->apiKey);
    }

    /**
     * Check if cloud PDF (html2pdf.app) is configured and enabled.
     */
    public function isCloudEnabled(): bool
    {
        return $this->cloudEnabled;
    }

    /**
     * Generate PDF from HTML string.
     */
    public function fromHtml(
        string $html,
        string $format = 'A4',
        string $orientation = 'Portrait',
        array $margins = [15, 15, 15, 15],
        int $timeout = 60
    ): string {
        if ($this->cloudEnabled) {
            try {
                return $this->generateWithHtml2Pdf($html, $format, $orientation, $margins, $timeout);
            } catch (\Throwable $e) {
                Log::warning('[PdfService] html2pdf.app failed, falling back to mPDF: ' . $e->getMessage());
            }
        }

        return $this->generateWithMpdf($html, $format, $orientation, $margins);
    }

    /**
     * Generate PDF from a Blade view.
     */
    public function fromView(
        string $view,
        array $data = [],
        string $format = 'A4',
        string $orientation = 'Portrait',
        array $margins = [15, 15, 15, 15]
    ): string {
        $html = view($view, $data)->render();
        return $this->fromHtml($html, $format, $orientation, $margins);
    }

    /**
     * Generate PDF and return as download response.
     */
    public function download(
        string $html,
        string $filename,
        string $format = 'A4',
        string $orientation = 'Portrait',
        array $margins = [15, 15, 15, 15]
    ): \Symfony\Component\HttpFoundation\StreamedResponse {
        $pdf = $this->fromHtml($html, $format, $orientation, $margins);

        return response()->streamDownload(
            fn () => print($pdf),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Generate PDF from view and return as download response.
     */
    public function downloadView(
        string $view,
        array $data,
        string $filename,
        string $format = 'A4',
        string $orientation = 'Portrait',
        array $margins = [15, 15, 15, 15]
    ): \Symfony\Component\HttpFoundation\StreamedResponse {
        $html = view($view, $data)->render();
        return $this->download($html, $filename, $format, $orientation, $margins);
    }

    /**
     * Generate PDF and save to storage.
     */
    public function saveToStorage(
        string $html,
        string $path,
        string $disk = 'public',
        string $format = 'A4',
        string $orientation = 'Portrait',
        array $margins = [15, 15, 15, 15]
    ): string {
        $pdf = $this->fromHtml($html, $format, $orientation, $margins);
        Storage::disk($disk)->put($path, $pdf);
        return $path;
    }

    /**
     * Quick helper: Portrait PDF.
     *
     * @param string $html The HTML content
     * @param string|array $formatOrMargins Paper format (e.g., 'A4', 'Letter') or margins array for backwards compat
     * @param array $margins Margins [top, right, bottom, left]
     */
    public function portrait(string $html, string|array $formatOrMargins = 'A4', array $margins = [15, 15, 15, 15]): string
    {
        if (is_array($formatOrMargins)) {
            return $this->fromHtml($html, 'A4', 'Portrait', $formatOrMargins);
        }

        return $this->fromHtml($html, $formatOrMargins, 'Portrait', $margins);
    }

    /**
     * Quick helper: Landscape PDF.
     *
     * @param string $html The HTML content
     * @param string|array $formatOrMargins Paper format (e.g., 'A4', 'Letter') or margins array for backwards compat
     * @param array $margins Margins [top, right, bottom, left]
     */
    public function landscape(string $html, string|array $formatOrMargins = 'A4', array $margins = [15, 15, 15, 15]): string
    {
        if (is_array($formatOrMargins)) {
            return $this->fromHtml($html, 'A4', 'Landscape', $formatOrMargins);
        }

        return $this->fromHtml($html, $formatOrMargins, 'Landscape', $margins);
    }

    /**
     * Generate PDF using html2pdf.app cloud API.
     * @see https://html2pdf.app/documentation/
     */
    protected function generateWithHtml2Pdf(
        string $html,
        string $format,
        string $orientation,
        array $margins,
        int $timeout
    ): string {
        $response = Http::timeout($timeout)
            ->post('https://api.html2pdf.app/v1/generate', [
                'apiKey' => $this->apiKey,
                'html' => $html,
                'format' => $format,
                'landscape' => strtolower($orientation) === 'landscape',
                'marginTop' => $margins[0],
                'marginRight' => $margins[1],
                'marginBottom' => $margins[2],
                'marginLeft' => $margins[3],
                'media' => 'print',
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('html2pdf.app API error: ' . $response->body());
        }

        return $response->body();
    }

    /**
     * Generate PDF using mPDF (local fallback).
     */
    protected function generateWithMpdf(
        string $html,
        string $format,
        string $orientation,
        array $margins
    ): string {
        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new Mpdf([
            'format' => $format,
            'orientation' => $orientation[0], // 'P' or 'L'
            'margin_top' => $margins[0],
            'margin_right' => $margins[1],
            'margin_bottom' => $margins[2],
            'margin_left' => $margins[3],
            'tempDir' => $tempDir,
            'default_font' => 'dejavusans',
            'default_font_size' => 10,
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }
}
