<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public static $wrap = 'product';
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
            'name' => $this->name ,
            'brand_id' => $this->brand_id ,
            'category_id' => $this->category_id ,
            'primary_image' => $this->primary_image ,
            'text' => $this->text ,
            'price' => $this->price ,
            'quantity' => $this->quantity ,
            'delivery_amount' => $this->delivery_amount ,
            'image' => ProductImageResource::collection($this->whenLoaded('images'))
        ];
    }
}
