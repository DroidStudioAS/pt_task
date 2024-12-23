<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportRequest extends FormRequest
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
            'import_type' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!config("imports.{$value}")) {
                    $fail('The selected import type is invalid.');
                }
            }],
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB limit per file
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'import_type.required' => 'Please select an import type.',
            'file.required' => 'Please select a file to import.',
            'file.file' => 'The uploaded file is invalid.',
            'file.mimes' => 'The file must be a CSV, XLS, or XLSX file.'
        ];
    }
}
