<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Response\ResponseHelper;

class AbstractFormRequest extends FormRequest
{
    /** @var ResponseHelper */
    protected $responseHelper;

    /**
     * @param ResponseHelper $responseHelper
     */
    public function __construct(ResponseHelper $responseHelper)
    {
        $this->responseHelper = $responseHelper;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        //Currectly considering only first error
        $firstError = array_values($errors)[0][0];

        throw new HttpResponseException(
            $this->responseHelper->sendErrorResponse($firstError, JsonResponse::HTTP_BAD_REQUEST)
        );
    }
}
