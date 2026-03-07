<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:courses,code',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return require __DIR__ . '/messages.php';
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title),
        ]);
    }
}
