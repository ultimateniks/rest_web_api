<?php

namespace App\Http\Requests;

use App\Order;

class OrderStatusRequest extends AbstractFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value !== Order::ASSIGNED_ORDER_STATUS) {
                        $fail('INVALID_STATUS');
                    }
                },
            ]
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'status.required' => 'status_is_invalid',
            'status.string' => 'status_is_invalid',
        ];
    }
}
