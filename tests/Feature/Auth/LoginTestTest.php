<?php

namespace Tests\Feature\Auth;

use App\Entity\User\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{

    public function testForm()
    {
        $response=$this->get('/login');
        $response
            ->assertStatus(200)
            ->assertSee('Login');
    }
    public function testErrors()
    {
        $response=$this->post('/login',[
            'email'=>'',
            'password'=>'',
            ]);
        $response
            ->assertStatus(302)
            ->assertSessionHasErrors(['email','password']);

    }

    public function testWait()
    {
        $user=factory(User::class)->create(['status'=>User::STATUS_WAIT,'role'=>User::ROLE_USER]);
        $response=$this->post('/login',[
            'email'=>$user->email,
            'password'=>'secret',
        ]);
        $response
            ->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHas('error','You need to confirm your account. Please check your email.');

    }

    public function testActive()
    {
        $user=factory(User::class)->create(['status'=>User::STATUS_ACTIVE,'role'=>User::ROLE_USER]);
        $response=$this->post('/login',[
            'email'=>$user->email,
            'password'=>'secret',
        ]);
        $response
            ->assertStatus(302)
            ->assertRedirect('/cabinet');
        $this->assertAuthenticated();


    }


}
