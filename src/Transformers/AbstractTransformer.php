<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use MyParcelCom\JsonApi\Interfaces\UrlGeneratorInterface;

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
        $this->validateModel($model);

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

        if ($inDataTag && $link && $link !== '') {
            $transformed['links'] = [
                'related' => $link,
            ];
        }

        return $transformed;
    }

    /**
     * @param Collection $collection
     * @return array
     */
    protected function transformCollection(Collection $collection): array
    {
        $result = [];
        foreach ($collection as $model) {
            $result[] = $this->transformerFactory->createFromModel($model)->transform($model);
        }

        return $result;
    }

    /**
     * @param Collection $collection
     * @return array
     */
    protected function getAttributesFromCollection(Collection $collection): array
    {
        $result = [];
        foreach ($collection as $model) {
            $result[] = $this->getAttributesFromModel($model);
        }

        return $result;
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
     * @param array  $ids
     * @param string $class
     * @param string $relatedLink
     * @return array
     */
    protected function transformRelationshipForIds(array $ids, string $class, $relatedLink = null): array
    {
        $relation = [
            'data' => array_map(
                function ($id) use ($class) {
                    return $this->transformRelationshipForId($id, $class, false);
                },
                $ids
            ),
        ];
        if ($relatedLink) {
            $relation['links'] = ['related' => $relatedLink];
        }

        return $relation;
    }

    /**
     * @param string $id
     * @param string $class
     * @param bool   $inDataTag
     * @return array
     */
    protected function transformRelationshipForId(string $id, string $class, $inDataTag = true): array
    {
        return $this->transformRelationship(new $class(['id' => $id]), $inDataTag);
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
        $this->validateModel($model);

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
        $this->validateModel($model);

        return '';
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
