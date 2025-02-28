<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Errors;

use MyParcelCom\JsonApi\Errors\InvalidCredentialsError;
use PHPUnit\Framework\TestCase;

class InvalidCredentialsErrorTest extends TestCase
{
    protected InvalidCredentialsError $error;

    protected function setUp(): void
    {
        parent::setUp();

        $this->error = new InvalidCredentialsError('12345', 'Some error description');
    }

    public function testItSetsPropertiesThroughConstructor(): void
    {
        $this->assertEquals('Invalid carrier credentials', $this->error->getTitle());
        $this->assertEquals('12345', $this->error->getErrorCode());
        $this->assertEquals('Some error description', $this->error->getDetail());
    }

    public function testItSetsErrorCode(): void
    {
        $this->assertEquals('98765', $this->error->setErrorCode('98765')->getErrorCode());
    }

    public function testItSetsErrorDescription(): void
    {
        $this->assertEquals('Other error description', $this->error->setDetail('Other error description')->getDetail());
    }

    public function testItSetsTitle(): void
    {
        $this->assertEquals('Foo bar error title', $this->error->setTitle('Foo bar error title')->getTitle());
    }

    public function testItSetsStatus(): void
    {
        $this->assertEquals(422, $this->error->setStatus(422)->getStatus());
    }

    public function testItSetsLinks(): void
    {
        $this->assertEquals(
            ['self' => 'https://foo.bar/com'],
            $this->error->setLinks(['self' => 'https://foo.bar/com'])->getLinks(),
        );
    }

    public function testItAddsALink(): void
    {
        $this->error->setLinks(['self' => 'https://foo.bar/com']);
        $this->error->addLink('next', 'https://next.page/page=next');
        $this->assertEquals(
            [
                'self' => 'https://foo.bar/com',
                'next' => 'https://next.page/page=next',
            ],
            $this->error->getLinks(),
        );
    }

    public function testItSetsMeta(): void
    {
        $this->error->setMeta([
            'carrier_response' => [
                'error' => 'Oh noes, everything went wrong.',
            ],
        ]);
        $this->assertEquals([
            'carrier_response' => [
                'error' => 'Oh noes, everything went wrong.',
            ],
        ], $this->error->getMeta());
    }

    public function testItAddsMeta(): void
    {
        $this->error->setMeta([
            'carrier_response' => [
                'error' => 'Oh noes, everything went wrong.',
            ],
        ]);
        $this->error->addMeta('carrier_status', '12345');
        $this->assertEquals([
            'carrier_response' => [
                'error' => 'Oh noes, everything went wrong.',
            ],
            'carrier_status'   => '12345',
        ], $this->error->getMeta());
    }

    public function testItSetsPointer(): void
    {
        $this->error->setPointer('data/attributes/foo-bar');
        $this->assertEquals('data/attributes/foo-bar', $this->error->getPointer());
        $this->assertEquals([
            'pointer' => 'data/attributes/foo-bar',
        ], $this->error->getSource());
    }
}
