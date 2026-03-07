<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateRequest extends FormRequest
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
        $course = $this->route('course');

        return [
            'title' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'code')->ignore($course->id),
            ],
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
