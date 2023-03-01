<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\AuthController;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_login_page_success(): void
    {
        $this->get(action([AuthController::class,'index']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.index');
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([AuthController::class,'signUp']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    /**
     * @test
     * @return void
     */
    public function it_forgot_page_success(): void
    {
        $this->get(action([AuthController::class,'forgot']))
            ->assertOk()
            ->assertSee('Забыли пароль')
            ->assertViewIs('auth.forgot-password');
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_in_success(): void
    {
        $password = '123456789';
        $user = User::factory()->create([
           'email' => 'testing2@mail.ru',
           'password' => bcrypt($password)
        ]);

        $request = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->post(
            action([AuthController::class,'signIn']),
            $request
        );

        $response->assertValid()->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test
     * @return void
     */
    public function it_logout_success(): void
    {
        $user = User::factory()->create([
            'email' => 'testing@mail.ru'
        ]);

        $this->actingAs($user)
            ->delete(action([AuthController::class,'logOut']));

        $this->assertGuest();
    }

    /**
     * @test
     * @return void
     */
    public function it_store_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = [
            'name' => 'David Poll',
            'email' => 'david@mail.ru',
            'password' => '123456789',
            'password_confirmation' => '123456789',
        ];

        $this->assertDatabaseMissing('users',[
            'email' => $request['email']
        ]);

        $response = $this->post(
            action([AuthController::class,'store']),
            $request
        );

        $response->assertValid();

        $this->assertDatabaseHas('users',[
            'email' => $request['email']
        ]);

        $user = User::query()->where('email',$request['email'])->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class,SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        Notification::assertSentTo($user,NewUserNotification::class);

        $response->assertRedirect(route('home'));
    }
}
