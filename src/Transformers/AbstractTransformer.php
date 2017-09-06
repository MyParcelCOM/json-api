<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

use MyParcelCom\Common\Contracts\UrlGeneratorInterface;

abstract class AbstractTransformer
{
    protected $urlGenerator;
    protected $transformerFactory;
    protected $type;

    public function __construct(UrlGeneratorInterface $urlGenerator, TransformerFactory $transformerFactory)
    {
        $this->urlGenerator = $urlGenerator;
        $this->transformerFactory = $transformerFactory;
    }

    /**
     * Transform the model to JSON Api output.
     *
     * @param object $model
     * @return array transformed data
     */
    public function transform($model): array
    {
        $this->validateModel($model);

        return array_filter([
            'id'            => $this->getId($model),
            'type'          => $this->getType(),
            'attributes'    => $this->getAttributes($model),
            'meta'          => $this->getMeta($model),
            'links'         => $this->getLinks($model),
            'relationships' => $this->getRelationships($model),
        ]);
    }

        /**
     * Transform the model relationships to JSON Api output.
     *
     * @param mixed $model
     * @param bool  $withLinks
     * @return array transformed relationships
     */
    protected function transformRelationship($model, $withLinks = false): array
    {
        $transformer = $this->transformerFactory->createFromModel($model);
        $relationship = $transformer->transformIdentifier($model);

        if ($withLinks) {
            $relationship = ['links' => ['self' => $transformer->getLinks($model)['self']], 'data' => $relationship];
        }

        return $relationship;
    }

    /**
     * @param $model
     * @return array
     */
    protected function getAttributesFromModel($model)
    {
        return $this->transformerFactory->createFromModel($model)->getAttributes($model);
    }

    /**
     * @param array $ids
     * @param mixed $model
     * @return array
     */
    protected function transformRelationshipsForIds(array $ids, $model): array
    {
        $this->validateModel($model);

        return array_map(
            function ($id) use ($model) {
                return $this->transformRelationship(new $model(['id' => $id]));
            },
            $ids
        );
    }

    /**
     * Transform a relationship identifier.
     *
     * @param mixed $model
     * @return array
     */
    public function transformIdentifier($model): array
    {
        $this->validateModel($model);

        return [
            'id'   => $this->getId($model),
            'type' => $this->getType(),
        ];
    }

    /**
     * @return string
     * @throws TransformerException
     */
    public function getType(): string
    {
        if (!isset($this->type)) {
            throw new TransformerException('Error no transformer resource type set for model');
        }

        return $this->type;
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getIncluded($model): array
    {
        $this->validateModel($model);

        return [];
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getRelationships($model): array
    {
        $this->validateModel($model);

        return [];
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getLinks($model): array
    {
        $this->validateModel($model);

        return [];
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getAttributes($model): array
    {
        $this->validateModel($model);

        return [];
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getMeta($model): array
    {
        $this->validateModel($model);

        return [];
    }

    /**
     * @param mixed $model
     * @return string
     */
    abstract public function getId($model): string;

    /**
     * @param mixed $model
     * @throws TransformerException
     */
    abstract protected function validateModel($model): void;
}
