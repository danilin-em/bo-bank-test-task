<?php

namespace Tests\Unit\DTOs;

use App\DTOs\TransferDTO;
use App\ValueObjects\Money;
use App\ValueObjects\ReferenceId;
use Tests\TestCase;

class TransferDTOTest extends TestCase
{
    public function test_create_creates_dto_with_all_fields(): void
    {
        // Arrange
        $fromAccountId = 1;
        $toAccountId = 2;
        $amount = new Money(10000);
        $referenceId = ReferenceId::generate();

        // Act
        $dto = TransferDTO::create($fromAccountId, $toAccountId, $amount, $referenceId);

        // Assert
        $this->assertEquals($fromAccountId, $dto->fromAccountId);
        $this->assertEquals($toAccountId, $dto->toAccountId);
        $this->assertEquals($amount, $dto->amount);
        $this->assertEquals($referenceId, $dto->referenceId);
    }

    public function test_create_with_auto_generated_reference_id(): void
    {
        // Arrange
        $fromAccountId = 1;
        $toAccountId = 2;
        $amount = new Money(5000);

        // Act
        $dto = TransferDTO::create($fromAccountId, $toAccountId, $amount);

        // Assert
        $this->assertEquals($fromAccountId, $dto->fromAccountId);
        $this->assertEquals($toAccountId, $dto->toAccountId);
        $this->assertEquals($amount, $dto->amount);
        $this->assertInstanceOf(ReferenceId::class, $dto->referenceId);
        
        // Verify reference ID is valid UUID format
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $dto->referenceId->value()
        );
    }

    public function test_readonly_properties_maintain_data_integrity(): void
    {
        // Arrange
        $dto = TransferDTO::create(1, 2, new Money(1000));

        // Assert
        $this->assertEquals(1, $dto->fromAccountId);
        $this->assertEquals(2, $dto->toAccountId);
        $this->assertEquals(1000, $dto->amount->value());
        $this->assertInstanceOf(ReferenceId::class, $dto->referenceId);
    }
}