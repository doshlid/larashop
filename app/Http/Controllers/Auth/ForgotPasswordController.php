<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Password;
use App\Http\Requests\ForgorPasswordFormRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ForgotPasswordController extends Controller
{
    public function page(): Factory|View|Application|RedirectResponse
    {
        return view('auth.forgot-password');
    }

    public function handle(ForgorPasswordFormRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT){
            flash()->info(__($status));

            return back();
        }
        return back()->withErrors(['email' => __($status)]);
    }
}
