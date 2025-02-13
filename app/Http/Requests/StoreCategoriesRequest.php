<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Allow the request for now, you can add proper authorization logic later
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string'
        ];
    }
}
