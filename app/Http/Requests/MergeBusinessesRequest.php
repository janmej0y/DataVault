<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class MergeBusinessesRequest extends FormRequest
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
            'business_ids' => ['required', 'array', 'min:2'],
            'business_ids.*' => ['string', 'exists:businesses,id'],
            'master_id' => ['required', 'string', 'exists:businesses,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $businessIds = collect($this->input('business_ids', []))->map(fn ($id) => (string) $id);
            $masterId = (string) $this->input('master_id');

            if (! $businessIds->contains($masterId)) {
                $validator->errors()->add('master_id', 'The master record must be included in the selected business IDs.');
            }
        });
    }
}
