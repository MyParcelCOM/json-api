<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Errors;

use MyParcelCom\JsonApi\Errors\InvalidInputContextError;
use PHPUnit\Framework\TestCase;

class InvalidInputContextErrorTest extends TestCase
{
    protected InvalidInputContextError $error;

    protected function setUp(): void
    {
        parent::setUp();

        $this->error = new InvalidInputContextError(
            '12345', 'Some error description', 'data/attributes/some-attribute',
        );
    }

    /** @test */
    public function testItSetsPropertiesThroughConstructor()
    {
        $this->assertEquals('Invalid input context', $this->error->getTitle());
        $this->assertEquals('12345', $this->error->getErrorCode());
        $this->assertEquals('Some error description', $this->error->getDetail());
        $this->assertEquals('data/attributes/some-attribute', $this->error->getPointer());
        $this->assertEquals([
            'pointer' => 'data/attributes/some-attribute',
        ], $this->error->getSource());
    }

    /** @test */
    public function testItSetsErrorCode()
    {
        $this->assertEquals('98765', $this->error->setErrorCode('98765')->getErrorCode());
    }

    /** @test */
    public function testItSetsErrorDescription()
    {
        $this->assertEquals('Other error description', $this->error->setDetail('Other error description')->getDetail());
    }

    /** @test */
    public function testItSetsTitle()
    {
        $this->assertEquals('Foo bar error title', $this->error->setTitle('Foo bar error title')->getTitle());
    }

    /** @test */
    public function testItSetsStatus()
    {
        $this->assertEquals(422, $this->error->setStatus(422)->getStatus());
    }

    /** @test */
    public function testItSetsLinks()
    {
        $this->assertEquals(
            ['self' => 'https://foo.bar/com'],
            $this->error->setLinks(['self' => 'https://foo.bar/com'])->getLinks(),
        );
    }

    /** @test */
    public function testItAddsALink()
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

    /** @test */
    public function testItSetsMeta()
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

    /** @test */
    public function testItAddsMeta()
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

    /** @test */
    public function testItSetsPointer()
    {
        $this->error->setPointer('data/attributes/foo-bar');
        $this->assertEquals('data/attributes/foo-bar', $this->error->getPointer());
        $this->assertEquals([
            'pointer' => 'data/attributes/foo-bar',
        ], $this->error->getSource());
    }
}
