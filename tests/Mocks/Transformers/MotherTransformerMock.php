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
     * @param mixed $model
     * @return array
     */
    public function transformIdentifier($model): array
    {
        return [
            'id'   => $this->getId($model),
            'type' => $this->getType(),
        ];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return 'mother';
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getIncluded($model): array
    {
        return [];
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getRelationships($model): array
    {
        return [];
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getLinks($model): array
    {
        return [
            'self' => '/link/to/mother',
        ];
    }

    /**
     * @param mixed $model
     * @return string
     */
    public function getLink($model): string
    {
        return '/link/to/mother';
    }

    /**
     * @param mixed $model
     * @return string
     */
    public function getRelationLink($model): string
    {
        return '/link/to/mother/relation';
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getAttributes($model): array
    {
        return [
            'age' => 'young',
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
