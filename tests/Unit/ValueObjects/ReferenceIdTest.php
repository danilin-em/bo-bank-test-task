<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\ReferenceId;
use InvalidArgumentException;
use Tests\TestCase;

class ReferenceIdTest extends TestCase
{
    public function test_constructor_creates_reference_id_with_valid_string(): void
    {
        // Arrange
        $id = 'TEST-REF-001';

        // Act
        $referenceId = new ReferenceId($id);

        // Assert
        $this->assertEquals($id, $referenceId->value());
        $this->assertEquals($id, (string) $referenceId);
    }

    public function test_constructor_throws_exception_for_empty_string(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be empty');

        // Act
        new ReferenceId('');
    }

    public function test_constructor_throws_exception_for_too_long_string(): void
    {
        // Arrange
        $longId = str_repeat('A', 256); // 256 characters

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Reference ID cannot be longer than 255 characters');

        // Act
        new ReferenceId($longId);
    }

    public function test_generate_creates_unique_uuid_reference_id(): void
    {
        // Act
        $referenceId1 = ReferenceId::generate();
        $referenceId2 = ReferenceId::generate();

        // Assert
        $this->assertInstanceOf(ReferenceId::class, $referenceId1);
        $this->assertInstanceOf(ReferenceId::class, $referenceId2);
        $this->assertNotEquals($referenceId1->value(), $referenceId2->value());
        
        // Check UUID format (8-4-4-4-12 pattern)
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $referenceId1->value()
        );
    }

    public function test_equals_returns_true_for_same_reference_ids(): void
    {
        // Arrange
        $id = 'TEST-REF-001';
        $referenceId1 = new ReferenceId($id);
        $referenceId2 = new ReferenceId($id);

        // Act & Assert
        $this->assertTrue($referenceId1->equals($referenceId2));
    }

    public function test_equals_returns_false_for_different_reference_ids(): void
    {
        // Arrange
        $referenceId1 = new ReferenceId('TEST-REF-001');
        $referenceId2 = new ReferenceId('TEST-REF-002');

        // Act & Assert
        $this->assertFalse($referenceId1->equals($referenceId2));
    }

    public function test_json_serialize_returns_reference_id_string(): void
    {
        // Arrange
        $id = 'TEST-REF-001';
        $referenceId = new ReferenceId($id);

        // Act
        $result = $referenceId->jsonSerialize();

        // Assert
        $this->assertEquals($id, $result);
    }

    public function test_accepts_various_valid_formats(): void
    {
        $validIds = [
            'SIMPLE-ID',
            'test-123',
            'REF_001',
            'DEPOSIT-2025-01-01-001',
            'TRANSFER-USER123-USER456-20250101T120000Z',
            str_repeat('A', 255), // Maximum length
            'a', // Minimum length
            '12345',
            'MIXED-case_123.ID',
        ];

        foreach ($validIds as $validId) {
            $referenceId = new ReferenceId($validId);
            $this->assertEquals($validId, $referenceId->value());
        }
    }

    public function test_generates_consistent_uuid_format(): void
    {
        // Generate multiple UUIDs and verify they all match the expected format
        for ($i = 0; $i < 10; $i++) {
            $referenceId = ReferenceId::generate();
            $uuid = $referenceId->value();
            
            // Verify UUID v4 format
            $this->assertMatchesRegularExpression(
                '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
                $uuid,
                "Generated UUID '{$uuid}' does not match expected format"
            );
        }
    }
}