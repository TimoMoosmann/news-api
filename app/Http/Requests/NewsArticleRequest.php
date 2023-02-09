<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewsArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'title' => ['unique:news_article', 'max:100'],
            'text' => ['min:20'],
            'author' => ['max:100'],
            'publish' => ['boolean'],
        ];

        if ($this->method() === 'POST') {
            $rules['title'][] = 'required';
            $rules['text'][] = 'required';
            $rules['author'][] = 'required';
            $rules['publish'][] = 'required';
        }

        return $rules;
    }
}
