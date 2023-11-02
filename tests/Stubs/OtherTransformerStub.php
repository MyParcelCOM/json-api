<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Tests\Stubs;

use Illuminate\Contracts\Routing\UrlGenerator;
use MyParcelCom\JsonApi\Transformers\AbstractTransformer;

class OtherTransformerStub extends AbstractTransformer
{
    protected mixed $dependency = null;

    public function getId($model): string
    {
        return 'otherId';
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

    public function getDependency()
    {
        return $this->dependency;
    }
}
