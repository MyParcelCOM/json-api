<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions\Tests;

use Exception;
use MyParcelCom\JsonApi\Exceptions\AuthException;
use MyParcelCom\JsonApi\Exceptions\CarrierApiException;
use MyParcelCom\JsonApi\Exceptions\ExternalRequestException;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\InvalidClientException;
use MyParcelCom\JsonApi\Exceptions\InvalidExternalErrorException;
use MyParcelCom\JsonApi\Exceptions\InvalidHeaderException;
use MyParcelCom\JsonApi\Exceptions\InvalidJsonSchemaException;
use MyParcelCom\JsonApi\Exceptions\InvalidScopeException;
use MyParcelCom\JsonApi\Exceptions\InvalidSecretException;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use MyParcelCom\JsonApi\Exceptions\ModelTypeException;
use MyParcelCom\JsonApi\Exceptions\NotFoundException;
use MyParcelCom\JsonApi\Exceptions\ResourceCannotBeModifiedException;
use MyParcelCom\JsonApi\Exceptions\ResourceConflictException;
use MyParcelCom\JsonApi\Exceptions\ResourceNotFoundException;
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
        $this->assertEquals(['carrier_response' => ['teapot']], $exception->getMeta());
        $this->assertEquals('HTCPCP', $exception->getPrevious()->getMessage());
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
}
