<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Resources;

use JsonSerializable;

class ResourceIdentifier implements JsonSerializable
{
    public function __construct(
        private string $id,
        private string $type,
        private ?string $parentId = null,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

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
