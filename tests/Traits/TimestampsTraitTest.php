<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Traits;

use Carbon\Carbon;
use MyParcelCom\JsonApi\Traits\TimestampsTrait;
use PHPUnit\Framework\TestCase;

class TimestampsTraitTest extends TestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new class (Carbon::now(), Carbon::now()) {
            use TimestampsTrait;

            public function __construct(
                private $created_at,
                private $updated_at,
            ) {
            }
        };
    }

    public function testGetUpdatedAt()
    {
        $this->assertInstanceOf(Carbon::class, $this->model->getUpdatedAt());
    }

    public function testGetCreatedAt()
    {
        $this->assertInstanceOf(Carbon::class, $this->model->getCreatedAt());
    }
}
