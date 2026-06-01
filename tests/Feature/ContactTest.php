<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_contact_form()
    {
        $response = $this->postJson('/contact-us/send', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'subject' => 'Test',
            'message' => 'This is a test message to debug the contact form.',
        ]);

        $response->dump();
        $response->assertStatus(200);
    }
}
