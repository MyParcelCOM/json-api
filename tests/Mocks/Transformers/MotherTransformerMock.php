<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Mocks\Transformers;

use MyParcelCom\JsonApi\Transformers\TransformerFactory;
use MyParcelCom\JsonApi\Transformers\TransformerInterface;

class MotherTransformerMock implements TransformerInterface
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

    public function transform($model): array
    {
        return [
            'id'   => $this->getId($model),
            'type' => $this->getType(),
        ];
    }

    public function transformIdentifier($model): array
    {
        return [
            'id'   => $this->getId($model),
            'type' => $this->getType(),
        ];
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

    public function __construct(TransformerFactory $transformerFactory)
    {
    }
}
