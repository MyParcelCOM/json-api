<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Stubs;

use DateTime;
use Illuminate\Contracts\Routing\UrlGenerator;
use MyParcelCom\JsonApi\Resources\ResourceIdentifier;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;
use UnitEnum;

class TransformerStub extends AbstractTransformer
{
    protected mixed $dependency = null;

    protected string $type = 'test';

    public function getId($model): string
    {
        return 'mockId';
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function setDependency($dependency): self
    {
        $this->dependency = $dependency;

        return $this;
    }

    public function getDependency(): mixed
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

    public function getLinkWithParentId(ResourceIdentifier $model): string
    {
        return $model->getParentId() . '/' . $model->getId();
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

    public function transformRelationshipForIdentifier(
        string $id,
        UnitEnum|string $type,
        string $class,
        string $parentId = null,
    ): array {
        return parent::transformRelationshipForIdentifier($id, $type, $class, $parentId);
    }

    public function transformRelationshipForIdentifiers(array $ids, UnitEnum|string $type, array $links = []): array
    {
        return parent::transformRelationshipForIdentifiers($ids, $type, $links);
    }
}
