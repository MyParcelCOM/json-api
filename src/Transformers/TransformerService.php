<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Resources\Interfaces\ResourcesInterface;

class TransformerService
{
    protected Paginator $paginator;

    protected array $includes = [];

    protected bool $multipleResult = false;

    public function __construct(
        protected TransformerFactory $transformerFactory,
    ) {
        $this->setPaginator(new Paginator());
    }

    public function setPaginator(Paginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    public function setIncludes(array $includes): self
    {
        $this->includes = $includes;

        return $this;
    }

    public function setMaxPageSize(int $maxPageSize): self
    {
        $this->paginator->setMaxPageSize($maxPageSize);

        return $this;
    }

    /**
     * Transform a builder to JSON Api output.
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
