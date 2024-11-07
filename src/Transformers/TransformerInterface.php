<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

interface TransformerInterface
{
    public function transform($model): array;

    public function transformIdentifier($model, bool $includeMeta = false): array;

    public function getType(): string;

    public function getIncluded($model): array;

    public function getRelationships($model): array;

    public function getLinks($model): array;

    public function getLink($model): string;

    public function getRelationLink($model): string;

    public function getAttributes($model): array;

    public function getMeta($model): array;

    public function getId($model): string|int|null;
}
