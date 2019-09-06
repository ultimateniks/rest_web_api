<?php

namespace App\Helpers;

class MessageHelper
{
    protected static $messages = [
        'status_is_invalid' => 'STATUS_IS_INVALID',
        'invalid_id' => 'INVALID_ID',
        'order_not_found' => 'ORDER_DOES_NOT_EXIST',
        'order_already_taken' => 'ORDER_ALREADY_BEEN_TAKEN',
        'success' => 'SUCCESS',
        'INVALID_PARAMETER_TYPE' => 'INVALID_PARAMETER_TYPE',
        'REQUEST_PARAMETER_MISSING' => 'REQUEST_PARAMETER_MISSING',
        'LATITUDE_OUT_OF_RANGE' => 'LATITUDE_RANGE_PASSED_IS_NOT_VALID',
        'LONGITUDE_OUT_OF_RANGE' => 'LONGITUDE_RANGE_PASSED_IS_NOT_VALID',
        'NO_DATA_FOUND' => 'NO_DATA_FOUND',
        'REQUESTED_ORIGIN_DESTINATION_SAME' => 'REQUESTED_ORIGIN_AND_DESTINATION_IS_SAME',
        'REQUEST_DENIED' => 'REQUEST_TO_GOOGLE_DISTANCE_API_IS_DENIED',
        'OVER_QUERY_LIMIT' => 'OVER_QUERY_LIMIT_FOR_GOOGLE_API_KEY',
        'GOOGLE_API_NULL_RESPONSE' => 'DISTANCE_API_RETURNS_NULL',
        'INVALID_PARAMETERS' => 'INVALID_PARAMETERS',
        'NOT_FOUND' => 'GEOCODING_API__FOR_ORIGIN_OR_DESTINATION_CANNOT_PAIRED',
        'ZERO_RESULTS' => 'API_NOT_FOUND_ANY_ROUTES_FOR_GIVEN_VALUES',
    ];

    /**
     * Provided translated message if key is provided, otherwise provided whole array of
     * key->translated_message pairs.
     *
     * @param string|null $key
     *
     * @return array|string|null
     */
    public function getMessage($key = null)
    {
        $messageList = self::$messages;

        if (null === $key) {
            return $messageList;
        }

        return isset($messageList[$key]) ? $messageList[$key] : null;
    }
}
