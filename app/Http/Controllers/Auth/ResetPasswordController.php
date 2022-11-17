<?php

namespace App\Http\Controllers\Auth;

use App\Models\ResetCodePassword;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends ApiController
{
    /**
     * @param  mixed $request
     * @return void
     */
    public function __invoke(ResetPasswordRequest $request)
    {
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->isExpire()) {
            return $this->jsonResponse(null, trans('passwords.code_is_expire'), 422);
        }

        $user = User::firstWhere('email', $passwordReset->email);

        // $user->update($request->only('password'));
        $user->update([
            "password" => Hash::make($request->password)
        ]);

        $passwordReset->where('code', $request->code)->delete();

        return $this->jsonResponse(null, trans('site.password_has_been_successfully_reset'), 200);
    }
}
