<?php

namespace Tests\Unit\DTOs;

use App\DTOs\UserUpdateDTO;
use App\ValueObjects\Email;
use Tests\TestCase;

class UserUpdateDTOTest extends TestCase
{
    public function test_constructor_creates_dto_with_all_fields(): void
    {
        // Arrange
        $name = 'John Doe';
        $email = new Email('john@example.com');
        $age = 30;

        // Act
        $dto = new UserUpdateDTO($name, $email, $age);

        // Assert
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($age, $dto->age);
    }

    public function test_constructor_creates_dto_with_null_fields(): void
    {
        // Act
        $dto = new UserUpdateDTO();

        // Assert
        $this->assertNull($dto->name);
        $this->assertNull($dto->email);
        $this->assertNull($dto->age);
    }

    public function test_constructor_creates_dto_with_partial_fields(): void
    {
        // Arrange
        $name = 'Jane Doe';
        $age = 25;

        // Act
        $dto = new UserUpdateDTO($name, null, $age);

        // Assert
        $this->assertEquals($name, $dto->name);
        $this->assertNull($dto->email);
        $this->assertEquals($age, $dto->age);
    }

    public function test_to_array_returns_correct_array_with_all_fields(): void
    {
        // Arrange
        $dto = new UserUpdateDTO(
            name: 'John Doe',
            email: new Email('john@example.com'),
            age: 30
        );

        // Act
        $result = $dto->toArray();

        // Assert
        $expected = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30,
        ];
        $this->assertEquals($expected, $result);
    }
}
