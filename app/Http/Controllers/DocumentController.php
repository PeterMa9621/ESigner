<?php

namespace App\Http\Controllers;

use App\Common\JWTSetting;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\Document as DocumentResource;
use App\Model\Document;
use App\Utility\FileUtil;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index() {
        return new DocumentCollection(Document::all());
    }

    public function get($id) {
        return new DocumentResource(Document::findOrFail($id));
    }

    public function create(Request $request) {
        $request->validate([
            'name' => 'required|max:255',
            'base64' => 'required',
            'signature_position_id' => 'required',
            'width' => 'required',
            'height' => 'required',
            'numPages' => 'required',
        ]);

        $path = FileUtil::base64ToPdf($request['base64']);

        $document = Document::create([
            'name' => $request['name'],
            'path' => $path,
            'signature_position_id' => $request['signature_position_id'],
            'width' => $request['width'],
            'height' => $request['height'],
            'numPages' => $request['numPages']
        ]);

        $documentResource = new DocumentResource($document);
        $documentArray = $documentResource->toArray($request);

        $key = JWT::encode(['data' => ['id' => $document->id], 'exp' => time() + 86400], JWTSetting::$DOCUMENT_KEY);

        return response()->json([
            'data' => [
                'document' => $documentArray,
                'key' => $key
            ]
        ], 201);
    }

    public function update($id, Request $request) {
        $document = Document::findOrFail($id);
        $document->update($request->all());

        return (new DocumentResource($document))
            ->response()
            ->setStatusCode(200);
    }

    public function delete($id) {
        $document = Document::findOrFail($id);
        $document->is_deleted = true;
        $document->save();
        return response()->json(null, 204);
    }
}
