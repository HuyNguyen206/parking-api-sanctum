<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Password;

/**
 * @group Auth
 */
class PasswordUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'current_password' => 'required|current_password'
        ]);

        $user = $request->user();
        $user->update(Arr::only($data, ['password']));

        return response()->json($user->only(['name', 'email', 'id']));
    }
}
