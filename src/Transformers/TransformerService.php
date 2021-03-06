<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;

class TransformerService
{
    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var Paginator */
    protected $paginator;

    /** @var array */
    protected $includes = [];

    /** @var bool */
    protected $multipleResult;

    /**
     * @param TransformerFactory $transformerFactory
     */
    public function __construct(TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
        $this->setPaginator(new Paginator());
    }

    /**
     * @param Paginator $paginator
     * @return $this
     */
    public function setPaginator(Paginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @param array $includes
     * @return $this
     */
    public function setIncludes(array $includes): self
    {
        $this->includes = $includes;

        return $this;
    }

    /**
     * @param int $maxPageSize
     * @return $this
     */
    public function setMaxPageSize(int $maxPageSize): self
    {
        $this->paginator->setMaxPageSize($maxPageSize);

        return $this;
    }

    /**
     * Transform a builder to JSON Api output.
     *
     * @param ResourcesInterface[] $resources
     * @return array
     */
    public function transformResources(ResourcesInterface ...$resources): array
    {
        $collections = [];

        $start = $this->paginator->getStart();
        $size = $this->paginator->getPerPage();

        // find the current page records from multiple resources
        foreach ($resources as $resource) {
            $count = $resource->count();
            // if there is more to print
            if ($size > 0) {
                $resource->offset($start)->limit($size);

                $collection = $resource->get();

                if (count($collection) > 0) {
                    $collections[] = $collection;
                    $start = 0;
                    $size -= count($collection);
                } else {
                    $start = $start - $count;
                }
            }
            // count up the total records found
            $this->paginator->addTotal($count);
        }

        $this->multipleResult = true;

        return $this->transformResource(...$collections);
    }

    /**
     * Transform the data to JSON Api.
     *
     * @param object[]|Collection[] $data
     * @return array
     * @throws TransformerException
     */
    public function transformResource(...$data): array
    {
        $items = [];

        foreach ($data as $datum) {
            if ($datum instanceof Collection) {
                $items[] = $this->transformerFactory->createTransformerCollection($datum);
            } elseif (is_object($datum)) {
                $items[] = $this->transformerFactory->createTransformerItem($datum);
            } else {
                throw new TransformerException('Cant transform type ' . gettype($datum));
            }
        }

        return (new TransformerResource($items))
            ->multipleResult($this->multipleResult)
            ->setRequestedIncludes($this->includes)
            ->setPaginator($this->paginator)
            ->getData();
    }
}
