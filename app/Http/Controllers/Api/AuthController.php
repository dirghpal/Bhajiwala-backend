<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        return handleApiRequest(function () use ($request) {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $this->response['msg'] = 'User Created';
            $this->response['data'] = $user;

            return response()->json($this->response, 201);
        });
    }

    public function login(LoginRequest $request)
    {
        return handleApiRequest(function () use ($request) {

            $user = User::where(
                'email',
                $request->email
            )->first();

            if (
                !$user ||
                !Hash::check(
                    $request->password,
                    $user->password
                )
            ) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'Invalid email or password',
                    401
                );
            }

            $token = $user
                ->createToken('auth_token')
                ->plainTextToken;

            $this->response['msg'] = 'Login Successfully';

            $this->response['data'] = [
                'user' => $user,
                'token' => $token,
            ];

            return response()->json($this->response);
        });
    }

    public function logout()
    {
        return handleApiRequest(function () {

            /** @var User|null $user */
            $user = Auth::user();

            /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
            $token = $user?->currentAccessToken();

            $token?->delete();

            $this->response['msg'] = 'Logout Successfully';
            $this->response['data'] = null;

            return response()->json($this->response);
        });
    }

    public function profile()
    {
        return handleApiRequest(function () {

            $this->response['msg'] = 'Profile Details';
            $this->response['data'] = Auth::user();

            return response()->json($this->response);
        });
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        return handleApiRequest(function () use ($request) {

            /** @var User|null $user */
            $user = Auth::user();

            if (!$user) {
                throw new \App\Http\Exceptions\ApiStatusException(
                    'User not found',
                    404
                );
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $this->response['msg'] =
                'Profile Updated Successfully';

            $this->response['data'] =
                $user->fresh();

            return response()->json($this->response);
        });
    }
}