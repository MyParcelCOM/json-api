<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use DateTime;
use MyParcelCom\JsonApi\Interfaces\UrlGeneratorInterface;
use MyParcelCom\JsonApi\Resources\ResourceIdentifier;

abstract class AbstractTransformer implements TransformerInterface
{
    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var string */
    protected $type;

    public function __construct(TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
    }

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @return $this
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): self
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }

    /**
     * Transform the model to JSON Api output.
     *
     * @param object $model
     * @return array transformed data
     */
    public function transform($model): array
    {
        return $this->arrayDeepFilter([
            'id'            => $this->getId($model),
            'type'          => $this->getType(),
            'attributes'    => $this->getAttributes($model),
            'meta'          => $this->getMeta($model),
            'links'         => $this->getLinks($model),
            'relationships' => $this->getRelationships($model),
        ]);
    }

    /**
     * Do a deep filter on an array to remove all null values
     *
     * @param array $array
     * @return array
     */
    private function arrayDeepFilter(array $array): array
    {
        $array = array_filter($array, function ($var) {
            return ($var !== null);
        });
        foreach ($array as $key => $subPart) {
            if (is_array($subPart)) {
                $array[$key] = $this->arrayDeepFilter($subPart);
                if (count($array[$key]) < 1) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    /**
     * Transform the model relationships to JSON Api output.
     *
     * @param mixed $model
     * @param bool  $inDataTag
     * @return array transformed relationships
     */
    protected function transformRelationship($model, $inDataTag = true): array
    {
        $transformer = $this->transformerFactory->createFromModel($model);
        $relationship = $transformer->transformIdentifier($model);
        $link = $transformer->getLink($model);
        $transformed = $inDataTag ? ['data' => $relationship] : $relationship;

        if ($inDataTag && $link) {
            $transformed['links'] = [
                'related' => $link,
            ];
        }

        return $transformed;
    }

    /**
     * @param $model
     * @return array|null
     */
    protected function getAttributesFromModel($model): ?array
    {
        if (!$model) {
            return null;
        }

        return $this->transformerFactory->createFromModel($model)->getAttributes($model);
    }

    /**
     * @param DateTime|null $dateTime
     * @return int|null
     */
    protected function getTimestamp(?DateTime $dateTime): ?int
    {
        if (!$dateTime) {
            return null;
        }

        return $dateTime->getTimestamp();
    }

    /**
     * @param string $id
     * @param string $type
     * @param string $class
     * @return array
     */
    protected function transformRelationshipForIdentifier(string $id, string $type, string $class): array
    {
        $resource = new ResourceIdentifier($id, $type);
        $transformer = $this->transformerFactory->createFromModel($class);
        $relationship = ['data' => $resource->jsonSerialize()];

        if (($link = $transformer->getLink($resource))) {
            $relationship['links'] = [
                'related' => $link,
            ];
        }

        return $relationship;
    }

    /**
     * @param string[]   $ids
     * @param string     $type
     * @param array|null $links
     * @return array
     */
    protected function transformRelationshipForIdentifiers(array $ids, string $type, array $links = null): array
    {
        return array_filter([
            'data'  => array_map(function ($id) use ($type) {
                return (new ResourceIdentifier($id, $type))->jsonSerialize();
            }, $ids),
            'links' => $links,
        ]);
    }

    /**
     * Transform a relationship identifier.
     *
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
        return array_filter([
            'self' => $this->getLink($model),
        ]);
    }

    /**
     * Get a link to the model
     *
     * @param mixed $model
     * @return string
     */
    public function getLink($model): string
    {
        return '';
    }

    /**
     * Get a link to the relation
     *
     * @param mixed $model
     * @return string
     */
    public function getRelationLink($model): string
    {
        return '';
    }

    /**
     * @param mixed $model
     * @return array
     */
    public function getAttributes($model): array
    {
        return [];
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
     * @param mixed $model
     * @return string
     */
    abstract public function getId($model): string;
}
