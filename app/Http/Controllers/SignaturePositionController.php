<?php

namespace App\Http\Controllers;

use App\Http\Resources\SignaturePosition as SignaturePositionResource;
use App\Model\SignaturePosition;
use Illuminate\Http\Request;

class SignaturePositionController extends Controller
{
    public function get($id) {
        return new SignaturePositionResource(SignaturePosition::findOrFail($id));
    }

    public function create(Request $request) {

        $request->validate([
            'x' => 'required',
            'y' => 'required',
            'page' => 'required'
        ]);

        $signaturePosition = SignaturePosition::create($request->all());

        return (new SignaturePositionResource($signaturePosition))
            ->response()
            ->setStatusCode(201);
    }

    public function update($id, Request $request) {
        $signaturePosition = SignaturePosition::findOrFail($id);
        $signaturePosition->update($request->all());

        return (new SignaturePositionResource($signaturePosition))
            ->response()
            ->setStatusCode(200);
    }
}
