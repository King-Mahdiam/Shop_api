<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public static $wrap = 'category';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);
        return [
          'id' => $this->id ,
            'parent_id' => $this->parent_id ,
            'name' => $this->name ,
            'text' => $this->text ,
            'child' => CategoryResource::collection($this->whenLoaded('child')) ,
            'products' => ProductResource::collection($this->whenLoaded('products')->load('images'))
        ];
    }
}
