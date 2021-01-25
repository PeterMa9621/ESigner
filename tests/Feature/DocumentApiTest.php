<?php

namespace Tests\Feature;

use App\Model\Document;
use App\Model\SignaturePosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentApiTest extends TestCase
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
        $signaturePosition = factory(SignaturePosition::class)->create();

        $response = $this->post('api/documents', [
            'name'                  => 'test',
            'base64'                => ';base64,aaa',
            'path'                  => '/document/aa.pdf',
            'is_signed'             => false,
            'width'                 => 300,
            'height'                => 600,
            'numPages'              => 1,
            'signature_position_id' => $signaturePosition->id,
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
        factory(SignaturePosition::class)->create();
        $document = factory(Document::class)->create();

        $response = $this->put('api/documents/' . $document->id, [
            'name'                  => 'a document'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['data' => ['name' => 'a document']]);
    }

    /**
     * Test DELETE method.
     *
     * @return void
     */
    public function testDeleteMethodTest()
    {
        factory(SignaturePosition::class)->create();
        $document = factory(Document::class)->create();

        $response = $this->delete('api/documents/' . $document->id);

        $response->assertStatus(204);
        assert(Document::findOrFail($document->id)->is_deleted == true);
    }
}
