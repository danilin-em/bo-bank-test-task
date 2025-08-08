<?php

namespace Tests\Unit\Casts;

use App\Casts\MoneyCast;
use App\ValueObjects\Money;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MoneyCastTest extends TestCase
{
    private MoneyCast $cast;
    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cast = new MoneyCast();
        $this->model = $this->createMock(Model::class);
    }

    public function test_get_returns_null_for_null_value(): void
    {
        $result = $this->cast->get($this->model, 'balance', null, []);

        $this->assertNull($result);
    }

    public function test_get_returns_money_object_for_integer_value(): void
    {
        $result = $this->cast->get($this->model, 'balance', 50000, []);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertEquals(50000, $result->value());
    }

    public function test_get_converts_string_to_integer_for_money_object(): void
    {
        $result = $this->cast->get($this->model, 'balance', '25000', []);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertEquals(25000, $result->value());
    }

    public function test_set_returns_null_for_null_value(): void
    {
        $result = $this->cast->set($this->model, 'balance', null, []);

        $this->assertNull($result);
    }

    public function test_set_returns_integer_for_money_object(): void
    {
        $money = new Money(75000);

        $result = $this->cast->set($this->model, 'balance', $money, []);

        $this->assertEquals(75000, $result);
    }

    public function test_set_converts_numeric_string_to_money_value(): void
    {
        $result = $this->cast->set($this->model, 'balance', '30000', []);

        $this->assertEquals(30000, $result);
    }

    public function test_set_converts_integer_to_money_value(): void
    {
        $result = $this->cast->set($this->model, 'balance', 15000, []);

        $this->assertEquals(15000, $result);
    }

    public function test_set_converts_float_to_money_value(): void
    {
        $result = $this->cast->set($this->model, 'balance', 100.50, []);

        $this->assertEquals(100, $result);
    }

    public function test_set_throws_exception_for_non_numeric_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a Money instance or numeric');

        $this->cast->set($this->model, 'balance', 'invalid', []);
    }

    public function test_set_throws_exception_for_array(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a Money instance or numeric');

        $this->cast->set($this->model, 'balance', [], []);
    }

    public function test_set_throws_exception_for_object(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a Money instance or numeric');

        $this->cast->set($this->model, 'balance', new \stdClass(), []);
    }

    public function test_round_trip_conversion_preserves_value(): void
    {
        $originalValue = 123456;

        // Set the value
        $setValue = $this->cast->set($this->model, 'balance', $originalValue, []);

        // Get the value back
        $getValue = $this->cast->get($this->model, 'balance', $setValue, []);

        $this->assertEquals($originalValue, $getValue->value());
    }

    public function test_round_trip_with_money_object(): void
    {
        $originalMoney = new Money(987654);

        // Set the money object
        $setValue = $this->cast->set($this->model, 'balance', $originalMoney, []);

        // Get it back as money object
        $getValue = $this->cast->get($this->model, 'balance', $setValue, []);

        $this->assertTrue($originalMoney->equals($getValue));
    }
}