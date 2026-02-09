<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnviroEduLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('enviroedu.login');
    }

    public function test_guest_redirected_to_login_when_visiting_protected_route(): void
    {
        $response = $this->get(route('student.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_successful_login_redirects_student_to_student_dashboard(): void
    {
        $user = User::factory()->student()->create([
            'email' => 'student@example.com',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'student@example.com',
            'password' => 'password',
            'role' => 'student',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_login_redirects_teacher_to_teacher_dashboard(): void
    {
        User::factory()->teacher()->create([
            'email' => 'teacher@example.com',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'teacher@example.com',
            'password' => 'password',
            'role' => 'teacher',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('teacher.dashboard'));
    }

    public function test_login_fails_when_role_does_not_match_account(): void
    {
        User::factory()->student()->create([
            'email' => 'student@example.com',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'student@example.com',
            'password' => 'password',
            'role' => 'teacher',
            '_token' => csrf_token(),
        ]);

        $response->assertSessionHasErrors('role');
        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'unknown@example.com',
            'password' => 'wrong',
            'role' => 'student',
            '_token' => csrf_token(),
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->student()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'), [
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
