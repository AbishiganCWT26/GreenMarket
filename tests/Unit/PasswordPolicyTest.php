<?php

namespace Tests\Unit;

use App\Services\PasswordPolicy;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PasswordPolicyTest extends TestCase
{
    public function test_password_policy_accepts_a_password_with_all_required_character_types(): void
    {
        $validator = Validator::make(
            ['password' => 'Abc123!@', 'password_confirmation' => 'Abc123!@'],
            ['password' => PasswordPolicy::rulesWithConfirmation()]
        );

        $this->assertFalse($validator->fails(), 'Password with all required criteria should pass.');
    }

    public function test_password_policy_rejects_a_password_missing_any_required_character_type(): void
    {
        $validator = Validator::make(
            ['password' => 'abc12345'],
            ['password' => PasswordPolicy::rulesWithConfirmation()]
        );

        $this->assertTrue($validator->fails(), 'Password missing uppercase, special, or spacing requirements should fail.');
    }
}
