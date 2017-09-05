<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

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

        $res['data'] = $data;
        $res['meta'] = ['total_pages' => $this->paginator->getCount()];

        if ($includes) {
            $res['includes'] = array_unique($includes, SORT_REGULAR); // remove duplicates
        }

        if ($links) {
            $res['links'] = $links;
        }

        return $res;
    }
}
