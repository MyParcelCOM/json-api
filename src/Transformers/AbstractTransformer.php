<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
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
     * @param bool  $withLinks
     * @return array transformed relationships
     */
    protected function transformRelationship($model, $withLinks = true): array
    {
        $transformer = $this->transformerFactory->createFromModel($model);
        $relationship = $transformer->transformIdentifier($model);

        if ($withLinks) {
            $relationship = [
                'links' => [
                    'related' => $transformer->getLink($model),
                ],
                'data'  => $relationship,
            ];
        }

        return $relationship;
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
     * @param array $ids
     * @param mixed $model
     * @param bool  $withLinks
     * @return array
     */
    protected function transformRelationshipsForIds(array $ids, $model, $withLinks = false): array
    {
        return array_map(
            function ($id) use ($model, $withLinks) {
                return $this->transformRelationshipsForId($id, $model, $withLinks);
            },
            $ids
        );
    }

    /**
     * @param string $id
     * @param mixed  $model
     * @param bool   $withLinks
     * @return array
     */
    protected function transformRelationshipsForId(string $id, $model, $withLinks = true): array
    {
        return $this->transformRelationship(new $model(['id' => $id]), $withLinks);
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
