<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;

class TransformerItem
{
    protected $resource;

    /** @var TransformerInterface */
    protected $transformer;

    /** @var TransformerFactory */
    protected $transformerFactory;

    /**
     * @param TransformerFactory $transformerFactory
     * @param mixed              $resource
     */
    public function __construct(TransformerFactory $transformerFactory, $resource)
    {
        $this->resource = $resource;
        $this->transformerFactory = $transformerFactory;
        $this->transformer = $transformerFactory->createFromModel($resource);
    }

    /**
     * Get the transformed data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->transformer->transform($this->resource);
    }

    /**
     * Get the items to add to the include list.
     *
     * @param array $relationships   the relationships that we want to include
     * @param array $alreadyIncluded the already included items
     * @return array
     */
    public function getIncluded(array $relationships = [], array $alreadyIncluded = []): array
    {
        /**
         * @note This method used to be more complex. It was checking if the
         *       resources that were to be included were already included.
         *       However the implementation was incomplete and instead of
         *       reducing the number of queries it was increasing them.
         */

        $included = [];

        $includedCallbacks = $this->transformer->getIncluded($this->resource);
        foreach ($includedCallbacks as $relationship => $callback) {
            if (in_array($relationship, $relationships)) {
                $resource = $callback();

                if ($resource === null) {
                    continue;
                }

                if ($resource instanceof Collection) {
                    $data = $this->transformerFactory->createTransformerCollection($resource)->getData();
                } else {
                    $data = [$this->transformerFactory->createTransformerItem($resource)->getData()];
                }

                $included = array_merge($included, $data);
            }

            if (array_key_exists($relationship, $relationships)) {
                if (!isset($resource)) {
                    $resource = $callback();
                }

                if ($resource instanceof Collection) {
                    $data = $this->transformerFactory->createTransformerCollection($resource)->getIncluded($relationships[$relationship]);
                } else {
                    $data = $this->transformerFactory->createTransformerItem($resource)->getIncluded($relationships[$relationship]);
                }

                $included = array_merge($included, $data);
            }

            // Unset the resource so the isset check works correctly on the next
            // iteration #phpvariablescoping
            unset($resource);
        }

        return $included;
    }
}
