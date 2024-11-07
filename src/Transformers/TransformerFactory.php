<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;

class TransformerFactory
{
    protected array $dependencies = [];

    protected array $transformerMap = [];

    /**
     * Local transformer cache.
     *
     * @var TransformerInterface[]
     */
    private array $transformers = [];

    public function setMapping(array $mapping): self
    {
        $this->transformerMap = $mapping;

        return $this;
    }

    /**
     * An example of the dependencies array:
     * [
     *     AbstractTransformer::class => [
     *         'setUrlGenerator' => function () {
     *             return new UrlGenerator();
     *         },
     *     ],
     * ]
     */
    public function setDependencies(array $dependencies): self
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    /**
     * Create a new transformer for a model.
     *
     * @param object|string $model
     */
    public function createFromModel($model): TransformerInterface
    {
        $modelClass = is_string($model) ? $model : get_class($model);

        if (isset($this->transformers[$modelClass])) {
            return $this->transformers[$modelClass];
        }

        foreach ($this->transformerMap as $class => $transformerClass) {
            if (is_a($modelClass, $class, true)) {
                $transformer = app()->make($transformerClass);
                return $this->transformers[$modelClass] = $this->injectDependencies($transformer);
            }
        }

        throw new TransformerException('No transformer found for class ' . get_class($model));
    }

    protected function injectDependencies(TransformerInterface $transformer): TransformerInterface
    {
        array_walk($this->dependencies, function ($dependencies, $class) use ($transformer) {
            if (!$transformer instanceof $class) {
                return;
            }

            array_walk($dependencies, function ($callable, $setter) use ($transformer) {
                $transformer->$setter(call_user_func($callable));
            });
        });

        return $transformer;
    }

    /**
     * Create a new transformer item for a model.
     *
     * @param object $model
     */
    public function createTransformerItem($model): TransformerItem
    {
        return new TransformerItem($this, $model);
    }

    /**
     * Create a new transformer collection for a model.
     */
    public function createTransformerCollection(Collection $collection): TransformerCollection
    {
        return new TransformerCollection($this, $collection);
    }
}
