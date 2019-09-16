<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Transformers;

use MyParcelCom\JsonApi\Tests\Mocks\Resources\FatherMock;
use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerInterface;

class FatherTransformerMock implements TransformerInterface
{
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
        return 'father';
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getIncluded($model): array
    {
        return [
            'father' => function () use ($model) {
                return new FatherMock((string)($this->getId($model) + 1));
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
            'self' => '/link/to/father',
        ];
    }

    /**
     * @param mixed $model
     * @return string
     */
    public function getLink($model): string
    {
        return '/link/to/father';
    }

    /**
     * @param mixed $model
     * @return string
     */
    public function getRelationLink($model): string
    {
        return '/link/to/father/relation';
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getAttributes($model): array
    {
        return [
            'age' => 'ancient',
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
