<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Transformers;

use Illuminate\Support\Collection;
use MyParcelCom\JsonApi\Http\Paginator;
use MyParcelCom\JsonApi\Http\Interfaces\RequestInterface;

class TransformerService
{
    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var Paginator */
    protected $paginator;

    /** @var array */
    protected $includes;

    /** @var bool */
    protected $multipleResult;

    public function __construct(RequestInterface $request, TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
        $this->paginator = $request->getPaginator();
        $this->includes = $request->getIncludes();
    }

    /**
     * Transform a builder to JSON Api output.
     *
     * @param Resources[] $resources
     * @return array
     */
    public function transformResources(...$resources): array
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
