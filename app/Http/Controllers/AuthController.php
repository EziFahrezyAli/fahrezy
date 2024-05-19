<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $requestData = $request->only(['name', 'email', 'password']);
            $validator = Validator::make($requestData, [
                'name' => 'required',
                'email' => 'required|email:rfc|unique:users,email',
                'password' => 'required'
            ]);

            if ($validator->fails()) throw new ValidationException($validator);

            $user = new User();
            $user->fill($requestData);
            $user->password = bcrypt($request->password);
            $user->save();

            DB::commit();
            return $this->sendResponse($user, 'Success created a user!');
        } catch (ValidationException $err) {
            DB::rollBack();
            return $this->sendError($err->validator->errors(), 'Validation Errors!');
        } catch (\Error $err) {
            DB::rollBack();
            return $this->sendError($err->getMessage());
        }
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:rfc',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $request->user()->tokens()->delete();
            $token = $request->user()->createToken('jwt_token', ["role:{$user->role}"])->plainTextToken;

            return $this->sendResponse([
                'user' => $user,
                'token' => $token
            ]);
        } else {

            throw ValidationException::withMessages(['email' => 'email or password incorect!']);
        }
    }

    public function oauthGoogleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function oauthGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $checkEmail = User::where([['email', $googleUser->email], ['google_id', null]])->first();

        if ($checkEmail) {
            $checkEmail->google_id = $googleUser->id;
            $checkEmail->google_token = $googleUser->token;
            $checkEmail->google_refresh_token = $googleUser->refreshToken;
            $checkEmail->save();
        }

        $user = User::updateOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
        ]);

        $checkToken = DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->exists();

        if ($checkToken) {
            $user->tokens()->delete();
        }

        $newToken = $user->createToken('jwt_token', ["role:user"])->plainTextToken;

        Auth::login($user);

        return $this->sendResponse(['jwtToken' => $newToken], 'Success login with Google!');
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return $this->sendResponse('Logout success!');
    }
}
