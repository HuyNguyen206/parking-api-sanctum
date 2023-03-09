<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Responsable\ResponseError;
use App\Responsable\ResponseSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Auth
 */
class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string']
        ]);
        try {
            if (!Auth::attempt($data)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect']
                ]);
            }
        }catch (\Throwable $ex){
            return new ResponseError(ex: $ex);
        }

        $user = User::query()->firstWhere('email', $data['email']);
        $device = substr($request->userAgent(), 0, 255);
        $expiredAt = $request->boolean('remember') ? null : now()->addMinutes(config('session.lifetime'));

        return new ResponseSuccess([
            'access_token' => $user->createToken($device, expiresAt: $expiredAt)->plainTextToken
        ], statusCode: Response::HTTP_CREATED);
    }
}
