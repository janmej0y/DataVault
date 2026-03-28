<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportBusinessesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'upload_file' => ['nullable', 'required_without:google_drive_url', 'file', 'mimes:csv,txt,xlsx,xls', 'max:20480'],
            'google_drive_url' => ['nullable', 'required_without:upload_file', 'url'],
        ];
    }
}
