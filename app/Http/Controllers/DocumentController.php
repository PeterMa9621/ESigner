<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentCollection;
use App\Http\Resources\Document as DocumentResource;
use App\Model\Document;
use App\Utility\FileUtil;
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

        return (new DocumentResource($document))
            ->response()
            ->setStatusCode(201);
    }

    public function update($id, Request $request) {
        $document = Document::whereId($id)->update($request->all());

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