<?php


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

function success_response($data = [], string $message = '', int $code = Response::HTTP_OK): JsonResponse
{
    $response = [
        'success' => true,
        'status'  => $code
    ];
    if ($data) $response['data'] = $data;
    if (strlen($message) > 1) $response['message'] = $message;
    return response()->json($response, $code);
}

function send_error(string $message, int $code = Response::HTTP_NOT_FOUND): JsonResponse
{
    $data = [
        'message' => $message,
        'code'    => $code,
        'success' => false
    ];
    return response()->json($data, $code);
}

// Send Validation Error
function validation_error($data, int $code = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
{
    $data = [
        'data'    => $data,
        'success' => false,
        'code'    => $code
    ];
    return response()->json($data);
}

// Generate Gender
function gender($rand = false): array|string
{
    $data = [
        'male'   => 'male',
        'female' => 'female'
    ];
    if ($rand === false) return $data;

    return array_rand($data);
}

// Get Random Religion
function religion($rand = false): array|string
{
    $data = ['islam' => 'islam', 'hindu' => 'hindu', 'christian' => 'christian'];
    return $rand === false ? $data : array_rand($data);
}

// Generate Random Phone Number
function random_phone(): string
{
    return '01' . rand(3, 9) . rand('11111111', '99999999');
}

// Random Status
function random_status(): string
{
    return array_rand(['active' => 'active', 'inactive' => 'inactive']);
}

/**
 * @param        $Image
 * @param string $dir
 * @return array
 */
#[ArrayShape(['name' => "string", 'path' => "string"])] function ImageUpload($Image, string $dir): array
{
    $file        = explode(';base64', $Image);
    $fileEx      = explode('/', $file[0]);
    $FinalFileEx = end($fileEx);
    $FileName    = uniqid() . date('Ymd-his') . '.' . $FinalFileEx;
    Storage::disk('public')->put($dir . '/' . $FileName, base64_decode($file[1]));
    return [
        'name' => $FileName,
        'path' => Storage::disk('public')->url($dir . '/' . $FileName)
    ];
}
