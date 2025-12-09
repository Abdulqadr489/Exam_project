<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApiResponseHelper
{
    public static function formatPaginatedFields(LengthAwarePaginator $dataRes, array $fieldMap)
    {
        return response()->json([
            'success'      => true,
            'message'      => 'List fetched successfully',
            'data'         => $dataRes->map(function ($item) use ($fieldMap) {
                $result = [];
                foreach ($fieldMap as $key => $value) {
                    if (is_int($key)) {
                        $result[$value] = data_get($item, $value);
                    } else {
                        $result[$key] = data_get($item, $value);
                    }
                }
                return $result;
            }),
            'total'        => $dataRes->total(),
            'current_page' => $dataRes->currentPage(),
            'per_page'     => $dataRes->perPage(),
        ]);
    }


    public static function success($message = 'Success', $data = null,  $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }


    public static function created($data = null, $message = 'Created successfully')
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], 201);
    }


    public static function error($message = 'Something went wrong', $data = null, $code = 400)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $code);
    }


    public static function unauthorized($message = 'Unauthorized', $data = null)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], 401);
    }


    public static function forbidden($message = 'Forbidden', $data = null)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], 403);
    }


    public static function notFound($message = 'Resource not found', $data = null)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], 404);
    }


    public static function validationError($errors, $message = 'Validation failed')
    {
        return response()->json([
            'data' => $errors,
            'message' => $message,
        ], 422);
    }


    public static function custom($data, $message = '', $code = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $code);
    }




}
