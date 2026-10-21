<?php

namespace Tests\Feature;

use Tests\TestCase;

class SitioWebTest extends TestCase
{
    public function test_sitio_web_guide_is_publicly_available(): void
    {
        $response = $this->get('/sitio-web');

        $response->assertOk();
    }
}
