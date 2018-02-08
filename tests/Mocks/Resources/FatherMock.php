<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Resources;

class FatherMock
{
    /** @var int */
    private static $idCounter = 0;

    /** @var string|null */
    private $id;

    /**
     * @param string $id
     */
    public function __construct(string $id = null)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        if (!isset($this->id)) {
            $this->id = (string)self::$idCounter++;
        }

        return $this->id;
    }
}
