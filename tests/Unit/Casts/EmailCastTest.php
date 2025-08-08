<?php

namespace Tests\Unit\Casts;

use App\Casts\EmailCast;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EmailCastTest extends TestCase
{
    private EmailCast $cast;
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new EmailCast();
        $this->model = $this->createMock(Model::class);
    }

    public function test_get_returns_null_for_null_value(): void
    {
        $result = $this->cast->get($this->model, 'email', null, []);

        $this->assertNull($result);
    }

    public function test_get_returns_email_object_for_valid_email(): void
    {
        $result = $this->cast->get($this->model, 'email', 'test@example.com', []);

        $this->assertInstanceOf(Email::class, $result);
        $this->assertEquals('test@example.com', $result->value());
    }

    public function test_get_creates_email_object_with_lowercase(): void
    {
        $result = $this->cast->get($this->model, 'email', 'TEST@EXAMPLE.COM', []);

        $this->assertInstanceOf(Email::class, $result);
        $this->assertEquals('test@example.com', $result->value());
    }

    public function test_get_throws_exception_for_invalid_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->cast->get($this->model, 'email', 'invalid-email', []);
    }

    public function test_set_returns_null_for_null_value(): void
    {
        $result = $this->cast->set($this->model, 'email', null, []);

        $this->assertNull($result);
    }

    public function test_set_returns_string_for_email_object(): void
    {
        $email = new Email('user@domain.com');

        $result = $this->cast->set($this->model, 'email', $email, []);

        $this->assertEquals('user@domain.com', $result);
    }

    public function test_set_converts_valid_email_string(): void
    {
        $result = $this->cast->set($this->model, 'email', 'admin@test.org', []);

        $this->assertEquals('admin@test.org', $result);
    }

    public function test_set_converts_and_normalizes_email_string(): void
    {
        $result = $this->cast->set($this->model, 'email', 'ADMIN@TEST.ORG', []);

        $this->assertEquals('admin@test.org', $result);
    }

    public function test_set_trims_whitespace_from_email(): void
    {
        $result = $this->cast->set($this->model, 'email', '  user@example.com  ', []);

        $this->assertEquals('user@example.com', $result);
    }

    public function test_set_throws_exception_for_invalid_email_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->cast->set($this->model, 'email', 'not-an-email', []);
    }

    public function test_set_throws_exception_for_empty_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        $this->cast->set($this->model, 'email', '', []);
    }

    public function test_set_throws_exception_for_whitespace_only_email(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');

        $this->cast->set($this->model, 'email', '   ', []);
    }

    public function test_set_throws_exception_for_too_long_email(): void
    {
        $longEmail = str_repeat('a', 250) . '@test.com';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->cast->set($this->model, 'email', $longEmail, []);
    }

    public function test_round_trip_conversion_preserves_normalized_email(): void
    {
        $originalEmail = 'USER@EXAMPLE.COM';

        // Set the value
        $setValue = $this->cast->set($this->model, 'email', $originalEmail, []);

        // Get the value back
        $getValue = $this->cast->get($this->model, 'email', $setValue, []);

        $this->assertEquals('user@example.com', $getValue->value());
    }

    public function test_round_trip_with_email_object(): void
    {
        $originalEmail = new Email('test@domain.net');

        // Set the email object
        $setValue = $this->cast->set($this->model, 'email', $originalEmail, []);

        // Get it back as email object
        $getValue = $this->cast->get($this->model, 'email', $setValue, []);

        $this->assertTrue($originalEmail->equals($getValue));
    }

    public function test_handles_various_valid_email_formats(): void
    {
        $validEmails = [
            'simple@example.com',
            'user.name@example.com',
            'user+tag@example.co.uk',
            'test123@sub.domain.org',
            'x@y.z'
        ];

        foreach ($validEmails as $email) {
            $result = $this->cast->set($this->model, 'email', $email, []);
            $this->assertIsString($result);
            $this->assertEquals(strtolower($email), $result);
        }
    }
}