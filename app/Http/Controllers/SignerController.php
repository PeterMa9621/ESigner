<?php

namespace App\Http\Controllers;

use App\Common\JWTSetting;
use App\Model\Document;
use App\Model\SignaturePosition;
use App\Http\Resources\SignaturePosition as SignaturePositionResource;
use App\Services\SignService;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;

class SignerController extends Controller
{
    /**
     * Show the document that will be signed.
     *
     * @param Integer $key
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($key) {
        try {
            $payload = JWT::decode($key, JWTSetting::$DOCUMENT_KEY, ['HS256']);
            $id = $payload->data->id;
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
        } catch (Exception $exception) {
            return abort(404);
        }
    }

    /**
     * Used to sign a document
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
