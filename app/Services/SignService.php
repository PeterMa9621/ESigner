<?php


namespace App\Services;

use FPDF;
use Illuminate\Support\Facades\Storage;

class SignService
{
    public static function signDocument($signatureBase64, $document, $signaturePosition) {
        $result = SignService::convertSignatureToPdf($signatureBase64, $document, $signaturePosition);
        $signaturePdfPath = $result[0];
        $imagePath = $result[1];
        $filename = explode('/', $document['path']);
        $filename = $filename[count($filename)-1];
        $originalPath = storage_path('app/' . $document['path']);
        $outputRelativeDir = '/documents/signed/';
        $outputDir = storage_path('app' . $outputRelativeDir);
        $outputPath = $outputDir . $filename;
        $outputRelativePath = $outputRelativeDir . $filename;
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        if(file_exists($outputPath))
            unlink($outputPath);

        $command = 'pdftk %s multistamp %s output %s';
        $command = sprintf($command, $originalPath, $signaturePdfPath, $outputPath);
        echo $command;
        exec($command);
        var_dump($output);
        unlink($signaturePdfPath);
        unlink($imagePath);
        return $outputRelativePath;
    }

    public static function convertSignatureToPdf($signatureBase64, $document, $signaturePosition) {
        $imageRelativePath = 'tmp/' . $document['id'] . '.png';
        $imagePath = storage_path('app/' . $imageRelativePath);
        $pdfPath = storage_path('app/tmp/' . $document['id'] . '.pdf');
        $base64 = explode(';base64,', $signatureBase64)[1];
        Storage::disk('local')->put($imageRelativePath, base64_decode($base64));

        $width = $document['width'];
        $height = $document['height'];
        $signaturePdf = new FPDF('P', 'mm', [$width, $height]);
        for($i=0; $i<$document['numPages']; $i++) {
            $signaturePdf->AddPage();
            if($i == $signaturePosition['page']-1) {
                $signaturePdf->Image($imagePath, $signaturePosition['x'], $signaturePosition['y'], 50, 25);
            }
        }

        $signaturePdf->Output('F', $pdfPath);
        return [$pdfPath, $imagePath];
    }
}