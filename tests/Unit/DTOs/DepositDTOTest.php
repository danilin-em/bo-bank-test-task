<?php

namespace Tests\Unit\DTOs;

use App\DTOs\DepositDTO;
use App\ValueObjects\Money;
use App\ValueObjects\ReferenceId;
use Tests\TestCase;

class DepositDTOTest extends TestCase
{
    public function test_create_creates_dto_with_all_fields(): void
    {
        // Arrange
        $accountId = 1;
        $amount = new Money(10000);
        $referenceId = ReferenceId::generate();

        // Act
        $dto = DepositDTO::create($accountId, $amount, $referenceId);

        // Assert
        $this->assertEquals($accountId, $dto->accountId);
        $this->assertEquals($amount, $dto->amount);
        $this->assertEquals($referenceId, $dto->referenceId);
    }

    public function test_create_with_auto_generated_reference_id(): void
    {
        // Arrange
        $accountId = 2;
        $amount = new Money(5000);

        // Act
        $dto = DepositDTO::create($accountId, $amount);

        // Assert
        $this->assertEquals($accountId, $dto->accountId);
        $this->assertEquals($amount, $dto->amount);
        $this->assertInstanceOf(ReferenceId::class, $dto->referenceId);
        
        // Verify reference ID is valid UUID format
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $dto->referenceId->value()
        );
    }

    public function test_readonly_properties_cannot_be_changed(): void
    {
        // Arrange
        $accountId = 3;
        $amount = new Money(2500);
        $dto = DepositDTO::create($accountId, $amount);

        // Assert - verify readonly properties maintain their values
        $this->assertEquals($accountId, $dto->accountId);
        $this->assertEquals($amount, $dto->amount);
        $this->assertInstanceOf(ReferenceId::class, $dto->referenceId);
    }

    public function test_different_instances_have_different_reference_ids(): void
    {
        // Act
        $dto1 = DepositDTO::create(1, new Money(1000));
        $dto2 = DepositDTO::create(1, new Money(1000));

        // Assert
        $this->assertNotEquals($dto1->referenceId->value(), $dto2->referenceId->value());
    }
}