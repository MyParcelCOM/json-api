<?php declare(strict_types=1);

namespace MyParcelCom\Transformers;

use Illuminate\Support\Collection;
use MyParcelCom\Common\Contracts\JsonApiRequestInterface;
use MyParcelCom\Common\Http\Paginator;
use MyParcelCom\Model\Builder;
use MyParcelCom\Model\Model;

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

    public function __construct(JsonApiRequestInterface $request, TransformerFactory $transformerFactory)
    {
        $this->transformerFactory = $transformerFactory;
        $this->paginator = $request->getPaginator();
        $this->includes = $request->getIncludes();
    }

    /**
     * Transform a builder to JSON Api output.
     *
     * @param Builder[] $sets
     * @return array
     */
    public function transformResources(Builder ...$sets): array
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

        $this->multipleResult = true;
        return $this->transformResource(...$collections);
    }

    /**
     * Transform the data to JSON Api.
     *
     * @param Model|Collection[] $data
     * @return array
     * @throws TransformerException
     */
    public function transformResource( ...$data): array
    {
        $items = [];

        foreach ($data as $datum) {
            if ($datum instanceof Collection) {
                $items[] = $this->transformerFactory->createTransformerCollection($datum);
            } elseif ($datum instanceof Model) {
                $items[] = $this->transformerFactory->createTransformerItem($datum);
            }else{
                throw new TransformerException('Cant transform model of type ' . get_class($datum));
            }
        }

        return (new TransformerResource($items))
            ->multipleResult($this->multipleResult)
            ->setRequestedIncludes($this->includes)
            ->setPaginator($this->paginator)
            ->getData();
    }
}
