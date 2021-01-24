@extends('layouts.app')
@section('content')
<signer src="{{ $pdfBase64 }}" title="{{ $title }}" :document-id="{{ $documentId }}"
        :signature-position="{{ $signaturePosition }}" :has-signed="{{ $hasSigned?'true':'false' }}" />
@endsection