<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Resources;

class PersonMock
{
    /** @var int */
    private static $idCounter = 0;

    /** @var string */
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
