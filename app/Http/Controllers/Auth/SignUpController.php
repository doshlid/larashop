<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Password;
use App\Http\Requests\SignUpFormRequest;
use Domain\Auth\Actions\RegisterNewUserAction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SignUpController extends Controller
{
    public function page(): Factory|View|Application|RedirectResponse
    {
        return view('auth.sign-up');
    }

    public function handle(SignUpFormRequest $request, RegisterNewUserAction $action): RedirectResponse
    {

        $action(
            $request->get('name'),
            $request->get('email'),
            $request->get('password'),
        );

        return redirect()->intended(route('home'));
    }
}
