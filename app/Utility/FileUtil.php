<?php

namespace App\Utility;

use Exception;
use Illuminate\Support\Facades\Storage;

class FileUtil {
    public static function base64ToPdf($base64) {
        try {
            $filename = bin2hex(openssl_random_pseudo_bytes(16)) . '.pdf';
            $url = '/documents/' . $filename;
            $base64 = explode(';base64,', $base64)[1];
            $pdf_decoded = base64_decode($base64);
            Storage::disk('local')->put('/documents/' . $filename, $pdf_decoded);
            //file_put_contents(plugin_dir_path(__FILE__) . '../../uploads/' . $filename, $pdf_decoded);

            return $url;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
