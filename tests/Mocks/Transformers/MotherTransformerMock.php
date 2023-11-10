<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Transformers;

use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerInterface;

class MotherTransformerMock implements TransformerInterface
{
    public function __construct(TransformerFactory $transformerFactory)
    {
    }

    public function getId($model): string
    {
        return $model->getId();
    }

    public function transform($model): array
    {
        return [
            'id'   => $this->getId($model),
            'type' => $this->getType(),
        ];
    }

    public function transformIdentifier($model, bool $includeMeta = false): array
    {
        $identifier = [
            'id'   => $this->getId($model),
            'type' => $this->getType(),
        ];

        if ($includeMeta) {
            $identifier['meta'] = $this->getMeta($model);
        }

        return $identifier;
    }

    public function getType(): string
    {
        return 'mother';
    }

    public function getIncluded($model): array
    {
        return [];
    }

    public function getRelationships($model): array
    {
        return [];
    }

    public function getLinks($model): array
    {
        return [
            'self' => '/link/to/mother',
        ];
    }

    public function getLink($model): string
    {
        return '/link/to/mother';
    }

    public function getRelationLink($model): string
    {
        return '/link/to/mother/relation';
    }

    public function getAttributes($model): array
    {
        return [
            'age' => 'young',
        ];
    }

    public function getMeta($model): array
    {
        return [];
    }
}
