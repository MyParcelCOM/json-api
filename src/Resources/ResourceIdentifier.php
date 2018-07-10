<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use JsonSerializable;

class ResourceIdentifier implements JsonSerializable
{
    /** @var string */
    private $id;

    /** @var string */
    private $type;

    /**
     * @param string $id
     * @param string $type
     */
    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id'   => $this->getId(),
            'type' => $this->getType(),
        ];
    }
}
