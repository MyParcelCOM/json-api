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
    protected $meta = [];

    /** @var MetaInterface[] */
    protected $metaObjects = [];

    /**
     * @param array $resources
     */
    public function __construct(array $resources)
    {
        $this->resources = $resources;
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
     * Transform the data to a json api formatted array.
     *
     * @return array
     * @throws TransformerException
     */
    public function toArray(): array
    {
        $data = [];
        $includes = [];

        if (!$this->paginator) {
            throw new TransformerException('No paginator set for transformer resource');
        }

        $links = $this->paginator->getLinks();

        foreach ($this->resources as $resource) {
            $data = array_merge($data, $resource->getData());
            $includes = array_merge($includes, $resource->getIncluded($this->requestedIncludes, $includes));
        }

        $meta = $this->meta;
        foreach ($this->metaObjects as $metaObject) {
            $meta = array_merge_recursive($meta, $metaObject->getMeta());
        }

        $res['data'] = $data;
        $res['meta'] = ['total_pages' => $this->paginator->getCount()] + $meta;

        if ($includes) {
            $res['includes'] = array_unique($includes, SORT_REGULAR); // remove duplicates
        }

        if ($links) {
            $res['links'] = $links;
        }

        return $res;
    }
}
