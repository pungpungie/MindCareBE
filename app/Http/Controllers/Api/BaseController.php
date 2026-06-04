<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Response sukses
     */
    public function successResponse($data, $message = 'Berhasil', $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Response error
     */
    public function errorResponse($message, $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Response untuk data yang tidak ditemukan
     */
    public function notFoundResponse($message = 'Data tidak ditemukan')
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Response untuk validasi error
     */
    public function validationErrorResponse($errors)
    {
        return $this->errorResponse('Validasi gagal', 422, $errors);
    }

    /**
     * Response untuk unauthorized
     */
    public function unauthorizedResponse($message = 'Tidak memiliki akses')
    {
        return $this->errorResponse($message, 401);
    }
}