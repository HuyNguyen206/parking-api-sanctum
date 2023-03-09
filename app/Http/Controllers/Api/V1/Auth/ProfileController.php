<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Auth
 */
class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user()->only(['email', 'name']), Response::HTTP_OK);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email' => ['email', Rule::unique('users')->ignoreModel($user = $request->user())],
            'name' => 'required|string'
        ]);

        $user->update($data);

        return \response()->json($user->only(['email', 'name']));
    }
}
