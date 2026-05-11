<?php

namespace App\Http\Requests;

use App\Rules\SafeMonitoringUrl;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'starts_with:http://,https://', new SafeMonitoringUrl],
            'method' => ['required', 'in:GET,HEAD'],
            'check_interval' => ['required', 'integer', 'min:1', 'max:1440'],
            'timeout' => ['required', 'integer', 'min:1', 'max:60'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
