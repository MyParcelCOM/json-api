<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Stubs;

use MyParcelCom\JsonApi\Interfaces\UrlGeneratorInterface;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;

class TransformerStub extends AbstractTransformer
{
    /** @var mixed */
    protected $dependency;

    /** @var string */
    protected $type = 'test';

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
}
