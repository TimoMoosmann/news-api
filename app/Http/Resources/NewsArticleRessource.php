<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsArticleRessource extends JsonResource
{
    private bool $singleRessource = false;

    public function setSingleRessource(): NewsArticleRessource {
        $this->singleRessource = true;
        return $this;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $representation = [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'created_at' => $this->created_at,
            'published_at' => is_null($this->published_at) ? '-' : $this->published_at,
        ];

        if ($this->singleRessource) {
            $representation = array_merge($representation, [
                'text' => $this->text,
            ]);
        }

        return $representation;
    }
}
