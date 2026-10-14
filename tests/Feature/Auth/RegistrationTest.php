<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * El registro público está desactivado: los usuarios se cargan por seeder.
     */
    public function test_registration_screen_is_not_available(): void
    {
        $response = $this->get('/register');

        $response->assertNotFound();
    }

    public function test_new_users_can_not_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertNotFound();
        $this->assertGuest();
    }
}
