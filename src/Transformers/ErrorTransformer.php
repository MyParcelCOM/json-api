<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use MyParcelCom\JsonApi\Exceptions\Interfaces\ExceptionInterface;

class ErrorTransformer
{
    /**
     * Transform given Exception into a valid JSON API response.
     *
     * @param  ExceptionInterface $exception
     * @return array
     */
    public function transform(ExceptionInterface $exception): array
    {
        return array_filter(
            [
                'id'     => $exception->getId(),
                'links'  => $exception->getLinks(),
                'status' => $exception->getStatus(),
                'code'   => $exception->getErrorCode(),
                'title'  => $exception->getTitle(),
                'detail' => $exception->getDetail(),
                'source' => $exception->getSource(),
                'meta'   => $exception->getMeta(),
            ]
        );
    }
}
