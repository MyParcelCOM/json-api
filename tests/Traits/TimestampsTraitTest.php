<?php declare(strict_types=1);

namespace MyParcelCom\Common\Tests\Traits;

use DateTime;
use MyParcelCom\Common\Traits\TimestampsTrait;
use PHPUnit\Framework\TestCase;

class TimestampsTraitTest extends TestCase
{
    private $model;

    public function setUp()
    {
        parent::setUp();

        $this->model = $this->getMockForTrait(TimestampsTrait::class);
    }

    public function testGetUpdatedAt()
    {
        $this->model->updated_at = new DateTime();

        $this->assertInstanceOf(DateTime::class, $this->model->getUpdatedAt());
    }

    public function testGetCreatedAt()
    {
        $this->model->created_at = new DateTime();

        $this->assertInstanceOf(DateTime::class, $this->model->getCreatedAt());
    }
}
