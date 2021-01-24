<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Document extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'path'                  => $this->path,
            'signed_path'           => $this->signed_path,
            'is_signed'             => $this->is_signed,
            'width'                 => $this->width,
            'height'                => $this->height,
            'numPages'              => $this->numPages,
            'signature_position_id' => $this->signature_position_id,
            'is_deleted'            => $this->is_deleted,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
