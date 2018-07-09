<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Stubs;

use DateTime;
use Illuminate\Database\Eloquent\Collection;
use MyParcelCom\JsonApi\Interfaces\UrlGeneratorInterface;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;

class TransformerStub extends AbstractTransformer
{
    /** @var mixed */
    protected $dependency;

    /** @var string */
    protected $type = 'test';

    /**
     * Helper function to reset the type for the abstract exception test.
     *
     * @return $this
     */
    public function clearType()
    {
        $this->type = null;

        return $this;
    }

    /**
     * @param mixed $model
     * @return string
     */
    public function getId($model): string
    {
        return 'mockId';
    }

    /**
     * @param object $model
     */
    public function validateModel($model): void
    {
    }

    /**
     * @return UrlGeneratorInterface
     */
    public function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    /**
     * @param mixed $dependency
     * @return $this
     */
    public function setDependency($dependency): self
    {
        $this->dependency = $dependency;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    public function getIncluded($model): array
    {
        return parent::getIncluded($model) + ['more' => 'things'];
    }

    public function getRelationships($model): array
    {
        return parent::getRelationships($model) + ['relation' => 'ship'];
    }

    public function getLink($model): string
    {
        return parent::getLink($model) . '#32';
    }

    public function getAttributes($model): array
    {
        return parent::getAttributes($model) + ['at' => 'tribute'];
    }

    public function getMeta($model): array
    {
        return parent::getMeta($model) + ['da' => 'ta'];
    }

    public function transformRelationship($model, $inDataTag = true): array
    {
        return parent::transformRelationship($model, $inDataTag);
    }

    public function getAttributesFromModel($model): ?array
    {
        return parent::getAttributesFromModel($model);
    }

    public function getTimestamp(?DateTime $dateTime): ?int
    {
        return parent::getTimestamp($dateTime);
    }

    public function transformRelationshipForIdentifier(string $id, string $type, string $class): array
    {
        return parent::transformRelationshipForIdentifier($id, $type, $class);
    }

    public function transformRelationshipForIdentifiers(array $ids, string $type, array $links = null): array
    {
        return parent::transformRelationshipForIdentifiers($ids, $type, $links);
    }

    public function transformRelationshipForId(string $id, string $class, $inDataTag = true): array
    {
        return parent::transformRelationshipForId($id, $class, $inDataTag);
    }

    public function transformRelationshipForIds(array $ids, string $class, $relatedLink = null): array
    {
        return parent::transformRelationshipForIds($ids, $class, $relatedLink);
    }
}
