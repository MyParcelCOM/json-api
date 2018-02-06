<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Exceptions;

class JsonApiErrorTransformer
{
    /**
     * Transform given JsonApiException into a valid jsonapi response.
     *
     * @param  JsonApiExceptionInterface $exception
     * @return array
     */
    public function transform(JsonApiExceptionInterface $exception): array
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
