<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'query' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'integer', 'min:100', 'max:50000'],
            'category' => ['nullable', 'string', 'max:100'],
            'search_type' => ['nullable', 'string', 'in:gps,address,city,neighborhood,cep,coordinates'],
        ];
    }
}
