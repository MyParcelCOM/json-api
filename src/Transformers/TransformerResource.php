<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

use MyParcelCom\Common\Contracts\MetaInterface;
use MyParcelCom\Common\Http\Paginator;

class TransformerResource
{
    /** @var array */
    protected $resources = [];

    /** @var Paginator */
    protected $paginator;

    /** @var array */
    protected $requestedIncludes = [];

    /** @var array */
    protected $data = [];

    /** @var array */
    protected $includes = [];

    /** @var array */
    protected $meta = [];

    /** @var array */
    protected $metaObjects = [];

    /** @var bool */
    protected $singleResult = false;

    /**
     * @param array $resources
     */
    public function __construct($resources)
    {
        $this->resources = $resources;
    }

    /**
     * @param bool $singleResult
     * @return TransformerResource
     */
    public function singleResult($singleResult = true): self
    {
        $this->singleResult = $singleResult;

        return $this;
    }

    /**
     * Set the paginator for the json output.
     *
     * @param Paginator $paginator
     * @return $this
     */
    public function setPaginator(Paginator $paginator): self
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Set what includes we need to include.
     *
     * @param array $requestedIncludes
     * @return $this
     */
    public function setRequestedIncludes(array $requestedIncludes): self
    {
        $this->requestedIncludes = $requestedIncludes;

        return $this;
    }

    /**
     * @param array|MetaInterface $meta
     * @return $this
     * @throws TransformerException
     */
    public function addMeta($meta)
    {
        if (is_array($meta)) {
            $this->meta = array_merge_recursive($meta, $this->meta);
        } elseif ($meta instanceof MetaInterface) {
            $this->metaObjects = $meta;
        } else {
            throw new TransformerException('Invalid meta object added, expected array or MetaInterface, got: ' . get_class($meta));
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $this->prepareData();
        if($this->singleResult){
            return $this->toArraySingle();
        }else{
            return $this->toArrayMultiple();
        }
    }

    /**
     * Transform the data to a json api formatted array.
     *
     * @return array
     * @throws TransformerException
     */
    public function toArrayMultiple(): array
    {
        if (!$this->paginator) {
            throw new TransformerException('No paginator set for transformer resource');
        }

        $res['data'] = $this->data;
        $res['meta'] = ['total_pages' => $this->paginator->getCount()] + $this->meta;

        if ($this->includes) {
            $res['includes'] = array_unique($this->includes, SORT_REGULAR); // remove duplicates
        }

        $links = $this->paginator->getLinks();
        if ($links) {
            $res['links'] = $links;
        }

        return $res;
    }

    /**
     * Transform the data to a json api formatted array.
     *
     * @return array
     * @throws TransformerException
     */
    public function toArraySingle(): array
    {
        $res['data'] = $this->data;

        if($this->meta){
            $res['meta'] = $this->meta;
        }

        if ($this->includes) {
            $res['includes'] = $this->includes;
        }

        return $res;
    }

    /**
     * @return void
     */
    public function prepareData(): void
    {
        foreach ($this->resources as $resource) {
            $this->data = array_merge($this->data, $resource->getData());
            $this->includes = array_merge($this->includes, $resource->getIncluded($this->requestedIncludes, $this->includes));
        }

        $this->includes = array_unique($this->includes, SORT_REGULAR); // remove duplicates

        foreach ($this->metaObjects as $metaObject) {
            $this->meta = array_merge_recursive($this->meta, $metaObject->getMeta());
        }
    }
}
