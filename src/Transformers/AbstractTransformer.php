<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use DateTime;
use Illuminate\Contracts\Routing\UrlGenerator;
use MyParcelCom\JsonApi\Resources\ResourceIdentifier;
use MyParcelCom\JsonApi\Traits\ArrayFilterTrait;

/** @template TModel */
abstract class AbstractTransformer implements TransformerInterface
{
    use ArrayFilterTrait;

    protected UrlGenerator $urlGenerator;

    protected string $type = '';

    public function __construct(
        protected TransformerFactory $transformerFactory,
    ) {
    }

    public function setUrlGenerator(UrlGenerator $urlGenerator): self
    {
        $this->urlGenerator = $urlGenerator;

        return $this;
    }

    /**
     * Transform the model to JSON Api output.
     *
     * @param TModel $model
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
     * Transform the model relationships to JSON Api output.
     */
    protected function transformRelationship(mixed $model, bool $inDataTag = true): array
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
     * @param TModel $model
     */
    protected function getAttributesFromModel($model): ?array
    {
        if (!$model) {
            return null;
        }

        return $this->transformerFactory->createFromModel($model)->getAttributes($model);
    }

    protected function getTimestamp(?DateTime $dateTime): ?int
    {
        return $dateTime?->getTimestamp();
    }

    protected function transformRelationshipForIdentifier(
        string $id,
        string $type,
        string $class,
        string $parentId = null,
    ): array {
        $resource = new ResourceIdentifier($id, $type, $parentId);
        $transformer = $this->transformerFactory->createFromModel($class);
        $relationship = ['data' => $resource->jsonSerialize()];

        if (($link = $transformer->getLink($resource))) {
            $relationship['links'] = [
                'related' => $link,
            ];
        }

        return $relationship;
    }

    protected function transformRelationshipForIdentifiers(array $ids, string $type, array $links = null): array
    {
        return array_filter([
            'data'  => array_map(fn ($id) => (new ResourceIdentifier($id, $type))->jsonSerialize(), $ids),
            'links' => $links,
        ]);
    }

    /**
     * Transform a relationship identifier.
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

        return $this->arrayDeepFilter($identifier);
    }

    /**
     * @throws TransformerException
     */
    public function getType(): string
    {
        if (empty($this->type)) {
            throw new TransformerException('Error no transformer resource `type` set for model');
        }

        return $this->type;
    }

    /**
     * @param TModel $model
     */
    public function getIncluded($model): array
    {
        return [];
    }

    /**
     * @param TModel $model
     */
    public function getRelationships($model): array
    {
        return [];
    }

    /**
     * @param TModel $model
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
     * @param TModel $model
     */
    public function getLink($model): string
    {
        return '';
    }

    /**
     * Get a link to the relation
     */
    public function getRelationLink(mixed $model): string
    {
        return '';
    }

    /**
     * @param TModel $model
     */
    public function getAttributes($model): array
    {
        return [];
    }

    /**
     * @param TModel $model
     */
    public function getMeta($model): array
    {
        return [];
    }

    /**
     * @param TModel $model
     */
    public function getId($model): string|int
    {
        return $model->getId();
    }
}
