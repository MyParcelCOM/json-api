<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Errors;

use MyParcelCom\JsonApi\Errors\CarrierDataNotFoundError;
use PHPUnit\Framework\TestCase;

class CarrierDataNotFoundErrorTest extends TestCase
{
    protected CarrierDataNotFoundError $error;

    protected function setUp(): void
    {
        parent::setUp();

        $this->error = new CarrierDataNotFoundError('-103', 'No carrier data was found for the given barcode');
    }

    public function testItSetsPropertiesThroughConstructor()
    {
        $this->assertEquals('Carrier data not found', $this->error->getTitle());
        $this->assertEquals('-103', $this->error->getErrorCode());
        $this->assertEquals('No carrier data was found for the given barcode', $this->error->getDetail());
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

    public function testItSetsPointer()
    {
        $this->error->setPointer('data/attributes/foo-bar');
        $this->assertEquals('data/attributes/foo-bar', $this->error->getPointer());
        $this->assertEquals([
            'pointer' => 'data/attributes/foo-bar',
        ], $this->error->getSource());
    }
}
