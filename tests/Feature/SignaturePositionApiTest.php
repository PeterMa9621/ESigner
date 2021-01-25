<?php

namespace Tests\Feature;

use App\Model\Document;
use App\Model\SignaturePosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignaturePositionApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test GET method.
     *
     * @return void
     */
    public function testGetMethodTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test POST method.
     *
     * @return void
     */
    public function testPostMethodTest()
    {
        $response = $this->post('api/signature_positions', [
            'x'     => 1,
            'y'     => 2,
            'page'  => 1
        ]);

        $response->assertStatus(201);
    }

    /**
     * Test PUT method.
     *
     * @return void
     */
    public function testPutMethodTest()
    {
        $signaturePosition = factory(SignaturePosition::class)->create();

        $response = $this->put('api/signature_positions/' . $signaturePosition->id, [
            'x'                  => 33
        ]);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['x' => 33]]);
    }
}
