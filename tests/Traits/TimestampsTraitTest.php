<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Traits;

use Illuminate\Support\Carbon;
use MyParcelCom\JsonApi\Traits\TimestampsTrait;
use PHPUnit\Framework\TestCase;

class TimestampsTraitTest extends TestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = $this->getMockForTrait(TimestampsTrait::class);
    }

    /** @test */
    public function testGetUpdatedAt()
    {
        $this->model->updated_at = Carbon::now();

        $this->assertInstanceOf(Carbon::class, $this->model->getUpdatedAt());
    }

    /** @test */
    public function testGetCreatedAt()
    {
        $this->model->created_at = Carbon::now();

        $this->assertInstanceOf(Carbon::class, $this->model->getCreatedAt());
    }
}
