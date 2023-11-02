<?php

declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use MyParcelCom\JsonApi\Http\Paginator;

class TransformerResource
{
    /** @var TransformerItem[] */
    protected array $resources = [];

    protected ?Paginator $paginator = null;

    protected array $requestedIncludes = [];

    protected array $data = [];

    protected array $includes = [];

    protected array $meta = [];

    protected bool $multipleResult = false;

    /**
     * @param TransformerItem[] $resources
     */
    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    public function multipleResult(bool $multipleResult = true): self
    {
        $this->multipleResult = $multipleResult;

        return $this;
    }

    /**
     * Set the paginator for the json output.
     */
    public function setPaginator(Paginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Set what includes we need to include.
     */
    public function setRequestedIncludes(array $requestedIncludes): self
    {
        $this->requestedIncludes = $requestedIncludes;

        return $this;
    }

    /**
     * @throws TransformerException
     */
    public function addMeta($meta): self
    {
        if (is_array($meta)) {
            $this->meta = array_merge_recursive($meta, $this->meta);
        } else {
            throw new TransformerException('Invalid meta object added, expected array or MetaInterface, got: ' . get_class($meta));
        }

        return $this;
    }

    public function getData(): array
    {
        $this->prepareData();

        return ($this->multipleResult)
            ? $this->toArrayMultiple()
            : $this->toArraySingle();
    }

    /**
     * Transform the data to a json api formatted array.
     *
     * @throws TransformerException
     */
    public function toArrayMultiple(): array
    {
        if (!$this->paginator) {
            throw new TransformerException('No paginator set for transformer resource');
        }

        $res['data'] = $this->data;
        $res['meta'] = [
                'total_pages'   => $this->paginator->getCount(),
                'total_records' => $this->paginator->getTotal(),
            ] + $this->meta;

        if ($this->includes) {
            $res['included'] = array_values(array_unique($this->includes, SORT_REGULAR)); // remove duplicates
        }

        $links = $this->paginator->getLinks();
        if ($links) {
            $res['links'] = $links;
        }

        return $res;
    }

    /**
     * Transform the data to a json api formatted array.
     */
    public function toArraySingle(): array
    {
        $res['data'] = $this->data;

        if ($this->meta) {
            $res['meta'] = $this->meta;
        }

        if ($this->includes) {
            $res['included'] = array_values($this->includes);
        }

        return $res;
    }

    public function prepareData(): void
    {
        foreach ($this->resources as $resource) {
            $resourceData = $resource->getData();
            $this->data = array_merge($this->data, $resourceData);
            $this->includes = array_merge(
                $this->includes,
                $resource->getIncluded($this->requestedIncludes, $this->includes, $resourceData)
            );
        }

        $this->includes = array_unique($this->includes, SORT_REGULAR); // remove duplicates
    }
}
