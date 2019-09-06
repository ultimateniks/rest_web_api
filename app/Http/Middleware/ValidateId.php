<?php

namespace App\Http\Middleware;

use App\Http\Response\ResponseHelper;
use Closure;
use Illuminate\Http\JsonResponse;

class ValidateId
{
    /**
     * @var ResponseHelper
     */
    protected $responsehelper;

    /**
     * @param ResponseHelper $response
     */
    public function __construct(
        ResponseHelper $responsehelper
    ) {
        $this->responsehelper = $responsehelper;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = $request->id;

        if (!is_numeric($id)) {
            return $this->responsehelper->sendErrorResponse('invalid_id', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
