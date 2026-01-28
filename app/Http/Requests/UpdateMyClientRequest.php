<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMyClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        foreach (['slug','name','client_prefix'] as $f) {
            if ($this->has($f)) $this->merge([$f => trim((string)$this->input($f))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes','required','string','max:250'],
            'slug' => ['sometimes','required','string','max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i'],
            'is_project' => ['sometimes','required', Rule::in(['0','1'])],
            'self_capture' => ['sometimes','required','string','size:1'],
            'client_prefix' => ['sometimes','required','string','max:4'],
            'client_logo' => ['sometimes','nullable','file','mimes:jpg,jpeg,png,webp','max:5120'],
            'address' => ['nullable','string'],
            'phone_number' => ['nullable','string','max:50'],
            'city' => ['nullable','string','max:50'],
        ];
    }
}