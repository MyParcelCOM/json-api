<?php declare(strict_types=1);

namespace MyParcelCom\Transformers\Tests\Stubs;

use MyParcelCom\Common\Contracts\UrlGeneratorInterface;
use MyParcelCom\Transformers\AbstractTransformer;

class TransformerStub extends AbstractTransformer
{
    /** @var mixed */
    protected $dependency;

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
}
