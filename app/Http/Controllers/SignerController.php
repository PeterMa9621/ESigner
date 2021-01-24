<?php

namespace App\Http\Controllers;

use App\Model\Document;
use App\Model\SignaturePosition;
use App\Http\Resources\SignaturePosition as SignaturePositionResource;
use App\Services\SignService;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;

class SignerController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @param Integer $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($id) {
        $document = Document::findOrFail($id);
        $isSigned = $document['is_signed'];
        if(!$isSigned)
            $pdf = file_get_contents(storage_path('app/' . $document['path']));
        else
            $pdf = file_get_contents(storage_path('app/' . $document['signed_path']));
        $pdfBase64 = base64_encode($pdf);
        $signaturePosition = new SignaturePositionResource(SignaturePosition::findOrFail($document['signature_position_id']));
        return view('signer', [
            'pdfBase64' => $pdfBase64,
            'title' => $document['name'],
            'signaturePosition' => json_encode($signaturePosition),
            'hasSigned' => $isSigned,
            'documentId' => $id
        ]);
    }

    public function sign($id, Request $request) {
        $document = Document::findOrFail($id);
        $signaturePosition = SignaturePosition::findOrFail($document['signature_position_id']);
        $signatureBase64 = $request['signature'];
        $outputRelativePath = SignService::signDocument($signatureBase64, $document, $signaturePosition);
        $document->signed_path = $outputRelativePath;
        $document->is_signed = true;
        $document->save();
        $pdf = file_get_contents(storage_path('app/' . $outputRelativePath));
        $pdfBase64 = base64_encode($pdf);
        return response()->json(['pdf' => $pdfBase64], 200);
    }
}
