<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Money;
use InvalidArgumentException;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    public function test_constructor_creates_money_with_valid_amount(): void
    {
        // Act
        $money = new Money(10000);

        // Assert
        $this->assertEquals(10000, $money->value());
        $this->assertEquals('10000.00', $money->toFormattedString());
    }

    public function test_constructor_throws_exception_for_negative_amount(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Money amount cannot be negative');

        // Act
        new Money(-1000);
    }

    public function test_zero_creates_zero_money(): void
    {
        // Act
        $money = Money::zero();

        // Assert
        $this->assertEquals(0, $money->value());
        $this->assertEquals('0.00', $money->toFormattedString());
    }

    public function test_add_returns_correct_sum(): void
    {
        // Arrange
        $money1 = new Money(10000);
        $money2 = new Money(5000);

        // Act
        $result = $money1->add($money2);

        // Assert
        $this->assertEquals(15000, $result->value());
        $this->assertEquals('15000.00', $result->toFormattedString());
    }

    public function test_subtract_returns_correct_difference(): void
    {
        // Arrange
        $money1 = new Money(10000);
        $money2 = new Money(3000);

        // Act
        $result = $money1->subtract($money2);

        // Assert
        $this->assertEquals(7000, $result->value());
        $this->assertEquals('7000.00', $result->toFormattedString());
    }

    public function test_equals_returns_true_for_same_amounts(): void
    {
        // Arrange
        $money1 = new Money(10000);
        $money2 = new Money(10000);

        // Act & Assert
        $this->assertTrue($money1->equals($money2));
    }

    public function test_is_greater_than_returns_correct_result(): void
    {
        // Arrange
        $money1 = new Money(10000);
        $money2 = new Money(5000);

        // Act & Assert
        $this->assertTrue($money1->isGreaterThan($money2));
        $this->assertFalse($money2->isGreaterThan($money1));
    }

    public function test_to_string_returns_formatted_string(): void
    {
        // Arrange
        $money = new Money(12345);

        // Act & Assert
        $this->assertEquals('12345.00', (string) $money);
    }

    public function test_json_serialize_returns_correct_format(): void
    {
        // Arrange
        $money = new Money(12345);

        // Act
        $result = $money->jsonSerialize();

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(12345, $result['cents']);
        $this->assertEquals('12345.00', $result['formatted']);
    }
}
