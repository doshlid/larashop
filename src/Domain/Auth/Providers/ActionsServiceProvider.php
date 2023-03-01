<?php

namespace Domain\Auth\Providers;

// use Illuminate\Support\Facades\Gate;
use Domain\Auth\Actions\RegisterNewUserAction;
use Domain\Auth\Contracts\RegisterNewUserContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class ActionsServiceProvider extends ServiceProvider
{
    public array $binding = [
        RegisterNewUserAction::class => RegisterNewUserContract::class
    ];
}
