<?php

namespace App\Http\Requests;

class AddOrderRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'origin' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (2 !== count($value)
                        || empty($value[0])
                        || empty($value[1])
                        || !is_numeric($value[0])
                        || !is_numeric($value[1])
                    ) {
                        $fail('INVALID_PARAMETERS');
                    }
                },
            ],
            'destination' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if (2 !== count($value)
                        || empty($value[0])
                        || empty($value[1])
                        || !is_numeric($value[0])
                        || !is_numeric($value[1])
                    ) {
                        $fail('INVALID_PARAMETERS');
                    }
                },
            ],
        ];
    }

    /**
     * Custom message for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'origin.required' => 'REQ_ORIGIN',
            'destination.required' => 'REQ_DESTINATION',
        ];
    }
}
