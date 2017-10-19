<?php declare(strict_types=1);

namespace MyParcelCom\Transformers\Tests\Stubs;

use MyParcelCom\Transformers\AbstractTransformer;

class TransformerStub extends AbstractTransformer
{
    /**
     * @param mixed $model
     * @return string
     */
    public function getId($model): string
    {
        return 'mockId';
    }

    public function validateModel($model): void
    {
    }
}
