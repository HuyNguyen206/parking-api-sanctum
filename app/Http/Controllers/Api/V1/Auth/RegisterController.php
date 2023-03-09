<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Responsable\ResponseError;
use App\Responsable\ResponseSuccess;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Auth
 */
class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'name' => 'string|required|max:255',
            'email' => ['required', 'string', 'email', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        try {
            $user = User::create($data);

            event(new Registered($user));

            $device = substr($request->userAgent() ?? '', 0, 255);
        } catch (\Throwable $ex) {
            return new ResponseError(ex: $ex);
        }

        return new ResponseSuccess([
            'access_token' => $user->createToken($device)->plainTextToken
        ], statusCode: Response::HTTP_CREATED);
    }
}
