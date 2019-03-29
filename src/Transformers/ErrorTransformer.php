<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use MyParcelCom\JsonApi\Exceptions\Interfaces\JsonSchemaErrorInterface;

class ErrorTransformer
{
    /**
     * Transform given Exception into a valid JSON API response.
     *
     * @param JsonSchemaErrorInterface $exception
     * @return array
     */
    public function transform(JsonSchemaErrorInterface $exception): array
    {
        return array_filter(
            [
                'id'     => (string)$exception->getId(),
                'links'  => $exception->getLinks(),
                'status' => (string)$exception->getStatus(),
                'code'   => (string)$exception->getErrorCode(),
                'title'  => (string)$exception->getTitle(),
                'detail' => (string)$exception->getDetail(),
                'source' => $exception->getSource(),
                'meta'   => $exception->getMeta(),
            ]
        );
    }
}
