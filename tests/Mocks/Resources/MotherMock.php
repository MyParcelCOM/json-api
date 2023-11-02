<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Resources;

class MotherMock
{
    private static int $idCounter = 0;

    public function __construct(
        private ?string $id = null
    ) {
    }

    public function getId(): string
    {
        if (!isset($this->id)) {
            $this->id = (string) self::$idCounter++;
        }

        return $this->id;
    }
}
