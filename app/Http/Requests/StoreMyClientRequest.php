<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMyClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    protected function prepareForValidation(): void
    {
        if ($this->has('slug')) $this->merge(['slug' => trim((string)$this->slug)]);
        if ($this->has('name')) $this->merge(['name' => trim((string)$this->name)]);
        if ($this->has('client_prefix')) $this->merge(['client_prefix' => trim((string)$this->client_prefix)]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:250'],
            'slug' => ['required','string','max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i'],
            'is_project' => ['required', Rule::in(['0','1'])],
            'self_capture' => ['required','string','size:1'],
            'client_prefix' => ['required','string','max:4'],
            'client_logo' => ['nullable','file','mimes:jpg,jpeg,png,webp','max:5120'],
            'address' => ['nullable','string'],
            'phone_number' => ['nullable','string','max:50'],
            'city' => ['nullable','string','max:50'],
        ];
    }
}