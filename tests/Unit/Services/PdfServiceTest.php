<?php

namespace Tests\Unit\Services;

use App\Services\PdfService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PdfServiceTest extends TestCase
{
    /**
     * Test isCloudEnabled returns true when api_key is configured.
     */
    public function test_is_cloud_enabled_returns_true_when_api_key_is_set(): void
    {
        Config::set('services.html2pdf.api_key', 'valid-api-key');

        $pdfService = new PdfService();

        $this->assertTrue($pdfService->isCloudEnabled());
    }

    /**
     * Test isCloudEnabled returns false when api_key is empty string.
     */
    public function test_is_cloud_enabled_returns_false_when_api_key_is_empty(): void
    {
        Config::set('services.html2pdf.api_key', '');

        $pdfService = new PdfService();

        $this->assertFalse($pdfService->isCloudEnabled());
    }

    /**
     * Test isCloudEnabled returns false when api_key is null.
     */
    public function test_is_cloud_enabled_returns_false_when_api_key_is_null(): void
    {
        Config::set('services.html2pdf.api_key', null);

        $pdfService = new PdfService();

        $this->assertFalse($pdfService->isCloudEnabled());
    }
}
