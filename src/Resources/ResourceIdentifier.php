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

    /** @var string */
    private $parentId;

    /**
     * @param string      $id
     * @param string      $type
     * @param string|null $parentId
     */
    public function __construct(string $id, string $type, string $parentId = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->parentId = $parentId;
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
     * @return string|null
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id'   => $this->getId(),
            'type' => $this->getType(),
        ];
    }
}
