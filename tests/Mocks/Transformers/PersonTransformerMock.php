<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Transformers;

use MyParcelCom\JsonApi\Tests\Mocks\Resources\FatherMock;
use MyParcelCom\JsonApi\Tests\Mocks\Resources\MotherMock;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerInterface;

class PersonTransformerMock implements TransformerInterface
{
    /** @var int */
    private static $idCounter = 0;

    /** @var string */
    private $id;

    /**
     * @param mixed $model
     * @return string
     */
    public function getId($model): string
    {
        return $model->getId();
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function transform($model): array
    {
        return [
            'id'   => $this->getId($model),
            'type' => $this->getType(),
        ];
    }

    /**
     * Transform a relationship identifier.
     *
     * @param mixed $model
     * @param bool  $includeMeta
     * @return array
     */
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

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'person';
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getIncluded($model): array
    {
        return [
            'mother' => function () use ($model) {
                return new MotherMock((string) ($this->getId($model) + 1));
            },
            'father' => function () use ($model) {
                return new FatherMock((string) ($this->getId($model) + 1));
            },
        ];
    }

    /**
     * @param mixed $model
     * @return array
     */
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

    /**
     * @param mixed $model
     * @return array
     */
    public function getLinks($model): array
    {
        return [
            'self' => '/link/to/person',
        ];
    }

    /**
     * @param mixed $model
     * @return string
     */
    public function getLink($model): string
    {
        return '/link/to/person';
    }

    /**
     * @param mixed $model
     * @return string
     */
    public function getRelationLink($model): string
    {
        return '/link/to/person/relation';
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getAttributes($model): array
    {
        return [
            'age' => 'old',
        ];
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getMeta($model): array
    {
        return [];
    }

    /**
     * @param TransformerFactory $transformerFactory
     */
    public function __construct(TransformerFactory $transformerFactory)
    {
    }
}
