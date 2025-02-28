<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Errors;

use MyParcelCom\JsonApi\Errors\GenericCarrierError;
use PHPUnit\Framework\TestCase;

class GenericCarrierErrorTest extends TestCase
{
    protected GenericCarrierError $error;

    protected function setUp(): void
    {
        parent::setUp();

        $this->error = new GenericCarrierError('12345', 'Some error description', 'data/attributes/some-attribute');
    }

    public function testItSetsPropertiesThroughConstructor()
    {
        $this->assertEquals('Generic carrier error', $this->error->getTitle());
        $this->assertEquals('12345', $this->error->getErrorCode());
        $this->assertEquals('Some error description', $this->error->getDetail());
        $this->assertEquals('data/attributes/some-attribute', $this->error->getPointer());
        $this->assertEquals([
            'pointer' => 'data/attributes/some-attribute',
        ], $this->error->getSource());
    }

    public function testItSetsId()
    {
        $this->assertEquals('idea', $this->error->setId('idea')->getId());
    }

    public function testItSetsErrorCode()
    {
        $this->assertEquals('98765', $this->error->setErrorCode('98765')->getErrorCode());
    }

    public function testItSetsErrorDescription()
    {
        $this->assertEquals('Other error description', $this->error->setDetail('Other error description')->getDetail());
    }

    public function testItSetsTitle()
    {
        $this->assertEquals('Foo bar error title', $this->error->setTitle('Foo bar error title')->getTitle());
    }

    public function testItSetsStatus()
    {
        $this->assertEquals(422, $this->error->setStatus(422)->getStatus());
    }

    public function testItSetsLinks()
    {
        $this->assertEquals(
            ['self' => 'https://foo.bar/com'],
            $this->error->setLinks(['self' => 'https://foo.bar/com'])->getLinks(),
        );
    }

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

    public function testPointer()
    {
        $error = new GenericCarrierError('12345', 'Some error description');
        $this->assertNull($error->getPointer());

        $error->setPointer('data/attributes/foo-bar');
        $this->assertEquals('data/attributes/foo-bar', $error->getPointer());
        $this->assertEquals([
            'pointer' => 'data/attributes/foo-bar',
        ], $error->getSource());
    }
}
