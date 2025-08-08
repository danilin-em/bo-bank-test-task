<?php

namespace Tests\Unit\Casts;

use App\Casts\ReferenceIdCast;
use App\ValueObjects\ReferenceId;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ReferenceIdCastTest extends TestCase
{
    private ReferenceIdCast $cast;
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new ReferenceIdCast();
        $this->model = $this->createMock(Model::class);
    }

    public function test_get_returns_null_for_null_value(): void
    {
        $result = $this->cast->get($this->model, 'reference_id', null, []);

        $this->assertNull($result);
    }

    public function test_get_returns_reference_id_object_for_string_value(): void
    {
        $result = $this->cast->get($this->model, 'reference_id', 'test-ref-123', []);

        $this->assertInstanceOf(ReferenceId::class, $result);
        $this->assertEquals('test-ref-123', $result->value());
    }

    public function test_get_returns_reference_id_object_for_uuid_string(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        
        $result = $this->cast->get($this->model, 'reference_id', $uuid, []);

        $this->assertInstanceOf(ReferenceId::class, $result);
        $this->assertEquals($uuid, $result->value());
        $this->assertTrue($result->isUuid());
    }

    public function test_get_throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be empty');

        $this->cast->get($this->model, 'reference_id', '', []);
    }

    public function test_get_throws_exception_for_whitespace_only_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be empty');

        $this->cast->get($this->model, 'reference_id', '   ', []);
    }

    public function test_get_throws_exception_for_too_long_string(): void
    {
        $longString = str_repeat('a', 256);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be longer than 255 characters');

        $this->cast->get($this->model, 'reference_id', $longString, []);
    }

    public function test_set_returns_null_for_null_value(): void
    {
        $result = $this->cast->set($this->model, 'reference_id', null, []);

        $this->assertNull($result);
    }

    public function test_set_returns_string_for_reference_id_object(): void
    {
        $referenceId = ReferenceId::fromString('custom-ref-456');

        $result = $this->cast->set($this->model, 'reference_id', $referenceId, []);

        $this->assertEquals('custom-ref-456', $result);
    }

    public function test_set_converts_valid_string(): void
    {
        $result = $this->cast->set($this->model, 'reference_id', 'transaction-789', []);

        $this->assertEquals('transaction-789', $result);
    }

    public function test_set_converts_uuid_string(): void
    {
        $uuid = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
        
        $result = $this->cast->set($this->model, 'reference_id', $uuid, []);

        $this->assertEquals($uuid, $result);
    }

    public function test_set_throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be empty');

        $this->cast->set($this->model, 'reference_id', '', []);
    }

    public function test_set_throws_exception_for_whitespace_only_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be empty');

        $this->cast->set($this->model, 'reference_id', '   ', []);
    }

    public function test_set_throws_exception_for_too_long_string(): void
    {
        $longString = str_repeat('b', 256);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be longer than 255 characters');

        $this->cast->set($this->model, 'reference_id', $longString, []);
    }

    public function test_round_trip_conversion_preserves_value(): void
    {
        $originalValue = 'payment-ref-12345';

        // Set the value
        $setValue = $this->cast->set($this->model, 'reference_id', $originalValue, []);

        // Get the value back
        $getValue = $this->cast->get($this->model, 'reference_id', $setValue, []);

        $this->assertEquals($originalValue, $getValue->value());
    }

    public function test_round_trip_with_reference_id_object(): void
    {
        $originalRef = ReferenceId::fromString('deposit-xyz-001');

        // Set the reference id object
        $setValue = $this->cast->set($this->model, 'reference_id', $originalRef, []);

        // Get it back as reference id object
        $getValue = $this->cast->get($this->model, 'reference_id', $setValue, []);

        $this->assertTrue($originalRef->equals($getValue));
    }

    public function test_round_trip_with_uuid(): void
    {
        $uuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';

        // Set the UUID string
        $setValue = $this->cast->set($this->model, 'reference_id', $uuid, []);

        // Get it back as reference id object
        $getValue = $this->cast->get($this->model, 'reference_id', $setValue, []);

        $this->assertEquals($uuid, $getValue->value());
        $this->assertTrue($getValue->isUuid());
    }

    public function test_handles_various_valid_reference_formats(): void
    {
        $validReferences = [
            'TXN-001',
            'payment_2025_001',
            'transfer-user-123-to-456',
            'DEPOSIT_ABC123',
            'ref.with.dots',
            '123456789',
            'a',
            str_repeat('x', 255) // Max length
        ];

        foreach ($validReferences as $ref) {
            $setValue = $this->cast->set($this->model, 'reference_id', $ref, []);
            $getValue = $this->cast->get($this->model, 'reference_id', $setValue, []);
            
            $this->assertEquals($ref, $getValue->value(), "Failed for reference: $ref");
        }
    }

    public function test_preserves_case_sensitivity(): void
    {
        $mixedCaseRef = 'Payment-ABC-123';

        $setValue = $this->cast->set($this->model, 'reference_id', $mixedCaseRef, []);
        $getValue = $this->cast->get($this->model, 'reference_id', $setValue, []);

        $this->assertEquals($mixedCaseRef, $getValue->value());
    }
}