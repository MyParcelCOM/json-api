<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

use Illuminate\Support\Collection;
use MyParcelCom\Common\Contracts\JsonApiRequestInterface;
use MyParcelCom\Common\Contracts\ResultSetInterface;
use MyParcelCom\Common\Http\Paginator;

class TransformerService
{
    /** @var TransformerFactory */
    protected $transformerFactory;

    /** @var Paginator */
    protected $paginator;

    /** @var array */
    protected $includes;

    public function __construct(JsonApiRequestInterface $request, TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
        $this->paginator = $request->getPaginator();
        $this->includes = $request->getIncludes();
    }

    /**
     * Transform a result set to JSON Api output.
     *
     * @param ResultSetInterface[] $sets
     * @return TransformerResource
     */
    public function transformResultSets(ResultSetInterface ...$sets): TransformerResource
    {
        $collections = [];

        $start = $this->paginator->getStart();
        $size = $this->paginator->getPerPage();

        // find the current page records from multiple sets
        foreach ($sets as $set) {
            $count = $set->count();
            // if there is more to print
            if ($size > 0) {
                $set->offset($start)->limit($size);

                $collection = $set->get();

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

        return $this->transform(...$collections)->setRequestedIncludes($this->includes)->setPaginator($this->paginator);
    }

    /**
     * Transform the data to JSON Api.
     *
     * @param mixed $data the data we are transforming
     * @return TransformerResource
     */
    protected function transform(...$data): TransformerResource
    {
        $items = [];

        foreach ($data as $datum) {
            if ($datum instanceof Collection) {
                $items[] = $this->transformerFactory->createTransformerCollection($datum);
            } else {
                $items[] = $this->transformerFactory->createTransformerItem($datum);
            }
        }

        $resource = new TransformerResource($items);

        return $resource;
    }
}
