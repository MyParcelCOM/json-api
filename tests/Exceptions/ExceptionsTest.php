<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Exceptions;

use Exception;
use MyParcelCom\JsonApi\Exceptions\AuthException;
use MyParcelCom\JsonApi\Exceptions\CarrierApiException;
use MyParcelCom\JsonApi\Exceptions\CarrierDataNotFoundException;
use MyParcelCom\JsonApi\Exceptions\ExternalRequestException;
use MyParcelCom\JsonApi\Exceptions\GenericCarrierException;
use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\InvalidClientException;
use MyParcelCom\JsonApi\Exceptions\InvalidCredentialsException;
use MyParcelCom\JsonApi\Exceptions\InvalidExternalErrorException;
use MyParcelCom\JsonApi\Exceptions\InvalidHeaderException;
use MyParcelCom\JsonApi\Exceptions\InvalidInputException;
use MyParcelCom\JsonApi\Exceptions\InvalidJsonSchemaException;
use MyParcelCom\JsonApi\Exceptions\InvalidScopeException;
use MyParcelCom\JsonApi\Exceptions\InvalidSecretException;
use MyParcelCom\JsonApi\Exceptions\MethodNotAllowedException;
use MyParcelCom\JsonApi\Exceptions\MissingBillingInformationException;
use MyParcelCom\JsonApi\Exceptions\MissingHeaderException;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use MyParcelCom\JsonApi\Exceptions\ModelTypeException;
use MyParcelCom\JsonApi\Exceptions\NotFoundException;
use MyParcelCom\JsonApi\Exceptions\RelationshipCannotBeModifiedException;
use MyParcelCom\JsonApi\Exceptions\ResourceCannotBeModifiedException;
use MyParcelCom\JsonApi\Exceptions\ResourceConflictException;
use MyParcelCom\JsonApi\Exceptions\ResourceNotFoundException;
use MyParcelCom\JsonApi\Exceptions\TooManyRequestsException;
use MyParcelCom\JsonApi\Exceptions\UnprocessableEntityException;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    /** @test */
    public function testAuthException()
    {
        $exception = new AuthException('Police', 90210, new Exception('Axel F'));

        $this->assertEquals('Police', $exception->getMessage());
        $this->assertEquals(90210, $exception->getStatus());
        $this->assertEquals('Axel F', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testCarrierApiException()
    {
        $exception = new CarrierApiException(418, ['teapot'], new Exception('HTCPCP'));

        $this->assertEquals(418, $exception->getStatus());
        $this->assertEquals([
            'carrier_response' => ['teapot'],
            'carrier_status'   => 418,
        ], $exception->getMeta());
        $this->assertEquals('HTCPCP', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testCarrierDataNotFoundException()
    {
        $exception = new CarrierDataNotFoundException(['data'], 404, new Exception('carrier'));

        $this->assertEquals(404, $exception->getStatus());
        $this->assertEquals(['data'], $exception->getErrors());
        $this->assertEquals('carrier', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testExternalRequestException()
    {
        $exception = new ExternalRequestException(1, 2, ['3'], new Exception('4'));

        $this->assertEquals(1, $exception->getStatus());
        $this->assertEquals(['external_status' => 2, 'external_error' => ['3']], $exception->getMeta());
        $this->assertEquals('4', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testInvalidAccessTokenException()
    {
        $exception = new InvalidAccessTokenException('Taken', new Exception('Liam'));

        $this->assertEquals('Taken', $exception->getMessage());
        $this->assertEquals('Liam', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testInvalidClientException()
    {
        $exception = new InvalidClientException();

        $this->assertEquals(403, $exception->getStatus());
    }

    /** @test */
    public function testInvalidExternalErrorException()
    {
        $exception = new InvalidExternalErrorException(new Exception("It Wasn't Me"));

        $this->assertEquals("It Wasn't Me", $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testInvalidHeaderException()
    {
        $exception = new InvalidHeaderException('HEAD');

        $this->assertEquals('HEAD', $exception->getMessage());
        $this->assertEquals(406, $exception->getStatus());
    }

    /** @test */
    public function testInvalidJsonSchemaException()
    {
        $exception = new InvalidJsonSchemaException(['fail'], new Exception('epic'));

        $this->assertEquals('epic', $exception->getPrevious()->getMessage());
        $this->assertEquals(['json_schema_errors' => ['fail']], $exception->getMeta());
        $this->assertEquals(['specification' => 'https://docs.myparcel.com/api-specification'], $exception->getLinks());
    }

    /** @test */
    public function testInvalidScopeException()
    {
        $exception = new InvalidScopeException(['slug', 'slag'], new Exception('Eridium'));

        $this->assertContains('slug, slag', $exception->getMessage());
        $this->assertEquals('Eridium', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testInvalidSecretException()
    {
        $exception = new InvalidSecretException(new Exception('welcome'));

        $this->assertEquals('welcome', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testMissingBillingInformationException()
    {
        $exception = new MissingBillingInformationException(['bil', 'ling'], new Exception('info'));

        $this->assertContains('bil, ling', $exception->getMessage());
        $this->assertEquals('info', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testMissingHeaderException()
    {
        $exception = new MissingHeaderException(['head', 'durr'], new Exception('hat'));

        $this->assertContains('head, durr', $exception->getMessage());
        $this->assertEquals('hat', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testMissingScopeException()
    {
        $exception = new MissingScopeException(['slug', 'slag'], new Exception('Eridium'));

        $this->assertContains('slug, slag', $exception->getMessage());
        $this->assertEquals('Eridium', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testMissingTokenException()
    {
        $exception = new MissingTokenException(new Exception('appreciation'));

        $this->assertEquals('appreciation', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testModelTypeException()
    {
        $exception1 = new ModelTypeException(new Exception(), 'Acception');
        $exception2 = new ModelTypeException(30931, '1337');

        $this->assertEquals('Invalid model of type `Exception`, expected model of type `Acception`', $exception1->getMessage());
        $this->assertEquals('Invalid model of type `30931`, expected model of type `1337`', $exception2->getMessage());
    }

    /** @test */
    public function testNotFoundException()
    {
        $exception = new NotFoundException('not found', new Exception('pants'));

        $this->assertEquals('pants', $exception->getPrevious()->getMessage());
        $this->assertEquals('not found', $exception->getMessage());
    }

    /** @test */
    public function testTooManyRequestsException()
    {
        $exception = new TooManyRequestsException('Too many requests.', new Exception('Go stand in the time-out corner!'));

        $this->assertEquals('Go stand in the time-out corner!', $exception->getPrevious()->getMessage());
        $this->assertEquals('Too many requests.', $exception->getMessage());
    }

    /** @test */
    public function testResourceCannotBeModifiedException()
    {
        $exception = new ResourceCannotBeModifiedException('solid', new Exception('frozen'));

        $this->assertEquals('frozen', $exception->getPrevious()->getMessage());
        $this->assertEquals('solid', $exception->getMessage());
    }

    /** @test */
    public function testResourceConflictException()
    {
        $exception = new ResourceConflictException('Konflict', new Exception('Mortal'));

        $this->assertEquals('The supplied resource `Konflict` is invalid.', $exception->getMessage());
        $this->assertEquals('Mortal', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testResourceNotFoundException()
    {
        $exception = new ResourceNotFoundException('API', new Exception('OMG'));

        $this->assertEquals('OMG', $exception->getPrevious()->getMessage());
        $this->assertEquals('One or more of the API resource could not be found.', $exception->getMessage());
    }

    /** @test */
    public function testUnprocessableEntityException()
    {
        $exception = new UnprocessableEntityException('RAW', new Exception('G-Star'));

        $this->assertEquals('G-Star', $exception->getPrevious()->getMessage());
        $this->assertEquals('RAW', $exception->getMessage());
    }

    /** @test */
    public function testMethodNotAllowedException()
    {
        $exception = new MethodNotAllowedException('Put', new Exception('Previous exception'));

        $this->assertEquals(405, $exception->getStatus());
        $this->assertEquals(10009, $exception->getCode());
        $this->assertEquals('The \'PUT\' method is not allowed on this endpoint.', $exception->getMessage());
        $this->assertEquals('Previous exception', $exception->getPrevious()->getMessage());
    }

    /** @test */
    public function testGenericCarrierErrorException()
    {
        $errors = [
            \Mockery::mock(JsonSchemaErrorInterface::class),
        ];
        $exception = new GenericCarrierException($errors);
        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals(500, $exception->getStatus());

        $exception->setStatus(300);
        $this->assertEquals(300, $exception->getStatus());

        $this->assertEquals([
            'foo' => 'bar',
        ], $exception->setMeta([
            'foo' => 'bar',
        ])->getMeta());
    }

    /** @test */
    public function testInvalidInputException()
    {
        $errors = [
            \Mockery::mock(JsonSchemaErrorInterface::class),
        ];
        $exception = new InvalidInputException($errors);
        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals(422, $exception->getStatus());

        $exception->setStatus(300);
        $this->assertEquals(300, $exception->getStatus());

        $this->assertEquals([
            'foo' => 'bar',
        ], $exception->setMeta([
            'foo' => 'bar',
        ])->getMeta());
    }

    /** @test */
    public function testRelationshipCannotBeModifiedException()
    {
        $relationshipType = 'shipments';

        $exception = new RelationshipCannotBeModifiedException($relationshipType, new Exception('Previous error.'));

        $this->assertEquals(403, $exception->getStatus());
        $this->assertEquals('10012', $exception->getCode());
        $this->assertEquals('Relationship cannot be modified.', $exception->getTitle());
        $this->assertEquals(
            "The relationship of type '{$relationshipType}' cannot be modified on this resource.",
            $exception->getMessage()
        );
    }

    /** @test */
    public function testInvalidCredentialsException()
    {
        $errors = [
            \Mockery::mock(JsonSchemaErrorInterface::class),
        ];
        $exception = new InvalidCredentialsException($errors);
        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals(401, $exception->getStatus());

        $exception->setStatus(300);
        $this->assertEquals(300, $exception->getStatus());

        $this->assertEquals([
            'foo' => 'bar',
        ], $exception->setMeta([
            'foo' => 'bar',
        ])->getMeta());
    }
}
