<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Exceptions;

use Exception;
use Mockery;
use MyParcelCom\JsonApi\Exceptions\AuthException;
use MyParcelCom\JsonApi\Exceptions\CarrierApiException;
use MyParcelCom\JsonApi\Exceptions\CarrierDataNotFoundException;
use MyParcelCom\JsonApi\Exceptions\ExternalRequestException;
use MyParcelCom\JsonApi\Exceptions\ForbiddenException;
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
use MyParcelCom\JsonApi\Exceptions\MissingHeaderException;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use MyParcelCom\JsonApi\Exceptions\ModelTypeException;
use MyParcelCom\JsonApi\Exceptions\NotFoundException;
use MyParcelCom\JsonApi\Exceptions\RelationshipCannotBeModifiedException;
use MyParcelCom\JsonApi\Exceptions\ResourceCannotBeModifiedException;
use MyParcelCom\JsonApi\Exceptions\ResourceConflictException;
use MyParcelCom\JsonApi\Exceptions\ResourceHandledBy3rdPartyException;
use MyParcelCom\JsonApi\Exceptions\ResourceNotFoundException;
use MyParcelCom\JsonApi\Exceptions\TooManyRequestsException;
use MyParcelCom\JsonApi\Exceptions\UnprocessableEntityException;
use PHPUnit\Framework\TestCase;

class ExceptionsTest extends TestCase
{
    public function testAuthException(): void
    {
        $exception = new AuthException('Police', 90210, new Exception('Axel F'));

        $this->assertEquals('Police', $exception->getMessage());
        $this->assertEquals(90210, $exception->getStatus());
        $this->assertEquals('Axel F', $exception->getPrevious()->getMessage());
    }

    public function testCarrierApiException(): void
    {
        $exception = new CarrierApiException(418, ['teapot'], new Exception('HTCPCP'));

        $this->assertEquals(418, $exception->getStatus());
        $this->assertEquals([
            'carrier_response' => ['teapot'],
            'carrier_status'   => 418,
        ], $exception->getMeta());
        $this->assertEquals('HTCPCP', $exception->getPrevious()->getMessage());
    }

    public function testCarrierDataNotFoundException(): void
    {
        $exception = new CarrierDataNotFoundException(['data'], 404, new Exception('carrier'));

        $this->assertEquals(404, $exception->getStatus());
        $this->assertEquals(['data'], $exception->getErrors());
        $this->assertEquals('carrier', $exception->getPrevious()->getMessage());
    }

    public function testExternalRequestException(): void
    {
        $exception = new ExternalRequestException(1, 2, ['3'], new Exception('4'));

        $this->assertEquals(1, $exception->getStatus());
        $this->assertEquals(['external_status' => 2, 'external_error' => ['3']], $exception->getMeta());
        $this->assertEquals('4', $exception->getPrevious()->getMessage());
    }

    public function testInvalidAccessTokenException(): void
    {
        $exception = new InvalidAccessTokenException('Taken', new Exception('Liam'));

        $this->assertEquals('Taken', $exception->getMessage());
        $this->assertEquals('Liam', $exception->getPrevious()->getMessage());
    }

    public function testInvalidClientException(): void
    {
        $exception = new InvalidClientException();

        $this->assertEquals(403, $exception->getStatus());
    }

    public function testInvalidExternalErrorException(): void
    {
        $exception = new InvalidExternalErrorException(new Exception("It Wasn't Me"));

        $this->assertEquals("It Wasn't Me", $exception->getPrevious()->getMessage());
    }

    public function testInvalidHeaderException(): void
    {
        $exception = new InvalidHeaderException('HEAD');

        $this->assertEquals('HEAD', $exception->getMessage());
        $this->assertEquals(406, $exception->getStatus());
    }

    public function testInvalidJsonSchemaException(): void
    {
        $exception = new InvalidJsonSchemaException(['fail'], new Exception('epic'));

        $this->assertEquals('epic', $exception->getPrevious()->getMessage());
        $this->assertEquals(['json_schema_errors' => ['fail']], $exception->getMeta());
        $this->assertEquals(['specification' => 'https://api-specification.myparcel.com'], $exception->getLinks());
    }

    public function testInvalidScopeException(): void
    {
        $exception = new InvalidScopeException(['slug', 'slag'], new Exception('Eridium'));

        $this->assertStringContainsString('slug, slag', $exception->getMessage());
        $this->assertEquals('Eridium', $exception->getPrevious()->getMessage());
    }

    public function testInvalidSecretException(): void
    {
        $exception = new InvalidSecretException(new Exception('welcome'));

        $this->assertEquals('welcome', $exception->getPrevious()->getMessage());
    }

    public function testMissingHeaderException(): void
    {
        $exception = new MissingHeaderException(['head', 'durr'], new Exception('hat'));

        $this->assertStringContainsString('head, durr', $exception->getMessage());
        $this->assertEquals('hat', $exception->getPrevious()->getMessage());
    }

    public function testMissingScopeException(): void
    {
        $exception = new MissingScopeException(['slug', 'slag'], new Exception('Eridium'));

        $this->assertStringContainsString('slug, slag', $exception->getMessage());
        $this->assertEquals('Eridium', $exception->getPrevious()->getMessage());
    }

    public function testMissingTokenException(): void
    {
        $exception = new MissingTokenException(new Exception('appreciation'));

        $this->assertEquals('appreciation', $exception->getPrevious()->getMessage());
    }

    public function testModelTypeException(): void
    {
        $exception1 = new ModelTypeException(new Exception(), 'Acception');
        $exception2 = new ModelTypeException(30931, '1337');

        $this->assertEquals(
            'Invalid model of type `Exception`, expected model of type `Acception`',
            $exception1->getMessage(),
        );
        $this->assertEquals('Invalid model of type `30931`, expected model of type `1337`', $exception2->getMessage());
    }

    public function testNotFoundException(): void
    {
        $exception = new NotFoundException('not found', new Exception('pants'));

        $this->assertEquals('pants', $exception->getPrevious()->getMessage());
        $this->assertEquals('not found', $exception->getMessage());
    }

    public function testTooManyRequestsException(): void
    {
        $exception = new TooManyRequestsException(
            'Too many requests.',
            new Exception('Go stand in the time-out corner!'),
        );

        $this->assertEquals('Go stand in the time-out corner!', $exception->getPrevious()->getMessage());
        $this->assertEquals('Too many requests.', $exception->getMessage());
    }

    public function testResourceCannotBeModifiedException(): void
    {
        $exception = new ResourceCannotBeModifiedException('solid', new Exception('frozen'));

        $this->assertEquals('frozen', $exception->getPrevious()->getMessage());
        $this->assertEquals('solid', $exception->getMessage());
    }

    public function testResourceConflictException(): void
    {
        $exception = new ResourceConflictException('Konflict', new Exception('Mortal'));

        $this->assertEquals('The supplied resource `Konflict` is invalid.', $exception->getMessage());
        $this->assertEquals('Mortal', $exception->getPrevious()->getMessage());
    }

    public function testResourceNotFoundException(): void
    {
        $exception = new ResourceNotFoundException('API', new Exception('OMG'));

        $this->assertEquals('OMG', $exception->getPrevious()->getMessage());
        $this->assertEquals('One or more of the API resource could not be found.', $exception->getMessage());
    }

    public function testUnprocessableEntityException(): void
    {
        $exception = new UnprocessableEntityException('RAW', new Exception('G-Star'));

        $this->assertEquals('G-Star', $exception->getPrevious()->getMessage());
        $this->assertEquals('RAW', $exception->getMessage());
    }

    public function testMethodNotAllowedException(): void
    {
        $exception = new MethodNotAllowedException('Put', new Exception('Previous exception'));

        $this->assertEquals(405, $exception->getStatus());
        $this->assertEquals(10009, $exception->getCode());
        $this->assertEquals('The \'PUT\' method is not allowed on this endpoint.', $exception->getMessage());
        $this->assertEquals('Previous exception', $exception->getPrevious()->getMessage());
    }

    public function testGenericCarrierErrorException(): void
    {
        $errors = [
            Mockery::mock(JsonSchemaErrorInterface::class),
        ];
        $exception = new GenericCarrierException($errors);
        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals(500, $exception->getStatus());

        $exception->setStatus(300);
        $this->assertEquals(300, $exception->getStatus());

        $this->assertEquals(
            ['foo' => 'bar'],
            $exception->setMeta(['foo' => 'bar'])->getMeta(),
        );
    }

    public function testInvalidInputException(): void
    {
        $errors = [
            Mockery::mock(JsonSchemaErrorInterface::class),
        ];
        $exception = new InvalidInputException($errors);
        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals(422, $exception->getStatus());

        $exception->setStatus(300);
        $this->assertEquals(300, $exception->getStatus());

        $this->assertEquals(
            ['foo' => 'bar'],
            $exception->setMeta(['foo' => 'bar'])->getMeta(),
        );
    }

    public function testRelationshipCannotBeModifiedException(): void
    {
        $relationshipType = 'shipments';

        $exception = new RelationshipCannotBeModifiedException($relationshipType, new Exception('Previous error.'));

        $this->assertEquals(403, $exception->getStatus());
        $this->assertEquals('10012', $exception->getCode());
        $this->assertEquals('Relationship cannot be modified.', $exception->getTitle());
        $this->assertEquals(
            "The relationship of type '{$relationshipType}' cannot be modified on this resource.",
            $exception->getMessage(),
        );
    }

    public function testInvalidCredentialsException(): void
    {
        $errors = [
            Mockery::mock(JsonSchemaErrorInterface::class),
        ];
        $exception = new InvalidCredentialsException($errors);
        $this->assertEquals($errors, $exception->getErrors());
        $this->assertEquals(401, $exception->getStatus());

        $exception->setStatus(300);
        $this->assertEquals(300, $exception->getStatus());

        $this->assertEquals(
            ['foo' => 'bar'],
            $exception->setMeta(['foo' => 'bar'])->getMeta(),
        );
    }

    public function testForbiddenException(): void
    {
        $exception = new ForbiddenException();

        $this->assertEquals('This user is not allowed to perform this action.', $exception->getMessage());
        $this->assertEquals(403, $exception->getStatus());

        $exception = new ForbiddenException('This user cannot do this specific action that they are trying to do!');
        $this->assertEquals(
            'This user cannot do this specific action that they are trying to do!',
            $exception->getMessage(),
        );
    }

    public function testResourceHandledBy3rdPartyException(): void
    {
        $exception = new ResourceHandledBy3rdPartyException('Order', 'Bol', new Exception('Previous error.'));

        self::assertEquals('Previous error.', $exception->getPrevious()->getMessage());
        self::assertEquals('One or more of the Order resource is handled by a 3rd party.', $exception->getMessage());
        self::assertEquals('10014', $exception->getCode());
        self::assertEquals(409, $exception->getStatus());
        self::assertEquals(['3rd_party' => 'Bol'], $exception->getMeta());
    }
}
