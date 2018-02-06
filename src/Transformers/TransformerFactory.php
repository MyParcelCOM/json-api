<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MyParcelCom\Common\Contracts\UrlGeneratorInterface;

class TransformerFactory
{
    /** @var array */
    protected $dependencies = [];

    /** @var array */
    protected $transformerMap = [];

    /**
     * Local transformer cache.
     *
     * @var AbstractTransformer[]
     */
    private $transformers = [];

    /**
     * @param array $mapping
     * @return $this
     */
    public function setMapping(array $mapping): self
    {
        $this->transformerMap = $mapping;

        return $this;
    }

    /**
     * @example
     *  [
     *      AbstractTransformer::class => [
     *          'setUrlGenerator' => function () {
     *              return new UrlGenerator();
     *          },
     *      ],
     *  ]
     *
     * @param array $dependencies
     * @return $this
     */
    public function setDependencies(array $dependencies): self
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    /**
     * Create a new transformer for a model.
     *
     * @param object $model
     * @return AbstractTransformer
     * @throws TransformerException
     */
    public function createFromModel($model): AbstractTransformer
    {
        $modelClass = get_class($model);

        if (isset($this->transformers[$modelClass])) {
            return $this->transformers[$modelClass];
        }

        foreach ($this->transformerMap as $class => $transformer) {
            if ($model instanceof $class) {
                return $this->transformers[$modelClass] = $this->injectDependencies(new $transformer($this));
            }
        }

        throw new TransformerException('No transformer found for class ' . get_class($model));
    }

    /**
     * @param AbstractTransformer $transformer
     * @return AbstractTransformer
     */
    protected function injectDependencies(AbstractTransformer $transformer): AbstractTransformer
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
     * @param Model $model
     * @return TransformerItem
     */
    public function createTransformerItem($model): TransformerItem
    {
        return new TransformerItem($this, $model);
    }

    /**
     * Create a new transformer collection for a model.
     *
     * @param Collection $collection
     * @return TransformerCollection self
     */
    public function createTransformerCollection(Collection $collection): TransformerCollection
    {
        return new TransformerCollection($this, $collection);
    }
}
