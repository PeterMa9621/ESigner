<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test home page.
     *
     * @return void
     */
    public function testHomePageTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test home page.
     *
     * @return void
     */
    public function testSignerPageTest()
    {
        $response = $this->get('/signer');

        $response->assertStatus(404);
    }

    /**
     * Test home page.
     *
     * @return void
     */
    public function testUploaderPageTest()
    {
        $response = $this->get('/uploader');

        $response->assertStatus(200);
    }

    /**
     * Test home page.
     *
     * @return void
     */
    public function testApiDocumentTest()
    {
        $response = $this->get('/api/documents');

        $response->assertStatus(200);
    }

    /**
     * Test home page.
     *
     * @return void
     */
    public function testApiSignaturePositionTest()
    {
        $response = $this->get('/api/signature_positions');

        $response->assertStatus(405);
    }
}
