<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Util\Exception;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @return JsonResponse
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = User::get();
        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) return validation_error($validator->errors());

        try {
            $credentials = ['email' => $request->email, 'password' => $request->password];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $data = [
                    'token' => $user->createToken('access_token')->accessToken,
                    'name'  => $user->name
                ];
                return success_response($data, __('message.login_success'), Response::HTTP_OK);
            }
            return send_error(__('auth.failed'), Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return send_error(__('message.failed'), $exception->getCode());
        }
    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => "required|min:4",
            'email'    => 'required|unique:users|min:6',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) return response()->json(['data' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        try {
            User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password)
            ]);
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = Auth::user();
                $data = [
                    'data' => $user->createToken('access_token')->accessToken,
                    'name' => $user->name
                ];
                return response()->json(['data' => $data], Response::HTTP_CREATED);
            }

        } catch (Exception $exception) {
            return send_error(__('message.user.create.error'), $exception->getCode());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): JsonResponse
    {
        if (Auth::check()) {
            $token = $request->user()->token();
            $token->revoke();
            return success_response('', __('message.logout'), Response::HTTP_OK);
        } else {
            return send_error('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }
    }
}
