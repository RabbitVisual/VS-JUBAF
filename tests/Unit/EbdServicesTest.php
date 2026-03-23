<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class EbdServicesTest extends TestCase
{
    public function test_ebd_models_present_or_skip()
    {
        if (! class_exists(\Modules\EBD\App\Models\EBDLesson::class)) {
            $this->markTestSkipped('Módulo EBD não encontrado — teste ignorado.');
        }

        $this->assertTrue(class_exists(\Modules\EBD\App\Models\EBDLesson::class));
    }
}
