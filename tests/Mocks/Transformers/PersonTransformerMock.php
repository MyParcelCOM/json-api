<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Transformers;

use MyParcelCom\JsonApi\Tests\Mocks\Resources\FatherMock;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\MotherMock;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerInterface;

class PersonTransformerMock implements TransformerInterface
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
        return 'person';
    }

    public function getIncluded($model): array
    {
        return [
            'mother' => function () use ($model) {
                return new MotherMock((string) (intval($this->getId($model)) + 1));
            },
            'father' => function () use ($model) {
                return new FatherMock((string) (intval($this->getId($model)) + 1));
            },
        ];
    }

    public function getRelationships($model): array
    {
        return [
            'mother' => [
                'data' => [
                    'id'   => 'mother-id-' . $this->getId($model),
                    'type' => 'mother',
                ],
            ],
            'father' => [
                'data' => [
                    'id'   => 'father-id-' . $this->getId($model),
                    'type' => 'father',
                ],
            ],
        ];
    }

    public function getLinks($model): array
    {
        return [
            'self' => '/link/to/person',
        ];
    }

    public function getLink($model): string
    {
        return '/link/to/person';
    }

    public function getRelationLink($model): string
    {
        return '/link/to/person/relation';
    }

    public function getAttributes($model): array
    {
        return [
            'age' => 'old',
        ];
    }

    public function getMeta($model): array
    {
        return [];
    }
}
