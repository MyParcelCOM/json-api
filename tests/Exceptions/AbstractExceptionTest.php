<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Exceptions;

use MyParcelCom\JsonApi\Exceptions\AbstractException;
use PHPUnit\Framework\TestCase;

class AbstractExceptionTest extends TestCase
{
    private AbstractException $exception;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exception = new class ('1', ['code' => '2', 'title' => '3'], 4) extends AbstractException {
        };
    }

    public function testId()
    {
        $this->assertEquals('1d', $this->exception->setId('1d')->getId());
    }

    public function testLinks()
    {
        $this->exception->setLinks(['l' => 'ink', 'miami' => 'ink'])->addLink('tat', 'too');

        $this->assertEquals(['l' => 'ink', 'miami' => 'ink', 'tat' => 'too'], $this->exception->getLinks());
    }

    public function testStatus()
    {
        $this->assertEquals(123, $this->exception->setStatus(123)->getStatus());
    }

    public function testErrorCode()
    {
        $this->assertEquals('36606', $this->exception->setErrorCode('36606')->getErrorCode());
    }

    public function testTitle()
    {
        $this->assertEquals('Señor', $this->exception->setTitle('Señor')->getTitle());
    }

    public function testDetail()
    {
        $this->assertEquals('eye', $this->exception->setDetail('eye')->getDetail());
    }

    public function testSource()
    {
        $this->assertEquals(['Counter' => 'Strike'], $this->exception->setSource(['Counter' => 'Strike'])->getSource());
    }

    public function testMeta()
    {
        $this->exception->setMeta(['m' => 'eta', 'b' => 'eta'])->addMeta('ta', 'da');

        $this->assertEquals(['m' => 'eta', 'b' => 'eta', 'ta' => 'da'], $this->exception->getMeta());
    }
}
