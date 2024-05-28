<?php

use App\Controller\MenuGestionController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class YourControllerTest extends TestCase
{
    public function testValidateDay()
    {
        // Create an instance of your controller class
        $controller = new MenuGestionController(); // Replace 'ClassName' with your actual controller class name

        // Get the reflection method for the private function validateDay
        $method = new ReflectionMethod(MenuGestionController::class, 'validateDay'); // Replace 'ClassName' with your actual controller class name
        $method->setAccessible(true); // Make the private method accessible

        // Mock the data to be validated
        $dayData = [
            'petit_dejeuner' => 'Some breakfast data',
            'dejeuner_a' => 'Some lunch A data',
            'dejeuner_b' => 'Some lunch B data',
            'diner' => 'Some dinner data',
        ];

        // Create a validator
        $validator = Validation::createValidator();

        // Call the private method and pass the data to it
        $violations = $method->invoke($controller, $dayData);

        // Assertions
        $this->assertInstanceOf(ConstraintViolationListInterface::class, $violations);
        $this->assertEquals(0, $violations->count());

        // Test with invalid data
        $invalidDayData = [
            'petit_dejeuner' => '', // Blank value
            'dejeuner_a' => 'Some lunch A data',
            'dejeuner_b' => 'Some lunch B data',
            'diner' => 'Some dinner data',
        ];

        // Call the private method again with invalid data
        $violations = $method->invoke($controller, $invalidDayData);

        // Assertions
        $this->assertInstanceOf(ConstraintViolationListInterface::class, $violations);
        $this->assertEquals(1, $violations->count());
        $this->assertEquals('This value should not be blank.', $violations[0]->getMessage());
        $this->assertEquals('petit_dejeuner', $violations[0]->getPropertyPath());
    }
}