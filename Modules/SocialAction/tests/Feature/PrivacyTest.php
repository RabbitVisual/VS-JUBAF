<?php

namespace Modules\SocialAction\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SocialAction\App\Models\SocialBeneficiary;
use App\Models\User;
use Modules\SocialAction\App\Services\PrivacyGuardService;

class PrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_beneficiary_data_is_encrypted()
    {
        $beneficiary = SocialBeneficiary::create([
            'full_name' => 'John Doe',
            'contact_info' => '123456789',
            'pastoral_notes' => 'Sensitive Note',
        ]);

        $this->assertDatabaseMissing('social_beneficiaries', [
            'full_name' => 'John Doe', // Should be encrypted
        ]);

        // Decrypt on access
        $retrieved = SocialBeneficiary::find($beneficiary->id);
        $this->assertEquals('John Doe', $retrieved->full_name);
    }

    public function test_privacy_guard_masks_data()
    {
        $service = new PrivacyGuardService();
        $masked = $service->maskData('123456789');
        $this->assertEquals('12*****89', $masked);
    }
}
