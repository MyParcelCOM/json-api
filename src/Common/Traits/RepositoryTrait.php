<?php declare(strict_types=1);

namespace MyParcelCom\Common\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MyParcelCom\Common\Exceptions\RepositoryException;
use MyParcelCom\Common\Resources\QueryResources;

/**
 * Repository generic functions
 */
trait RepositoryTrait
{
    /**
     * Get ALL from this model.
     *
     * @return QueryResources
     */
    public function getAll(): QueryResources
    {
        return new QueryResources($this->baseQuery());
    }

    /**
     * Get a specific model by it's id.
     *
     * @param string $id
     * @return Model|null
     */
    public function getById(string $id): ?Model
    {
        return $this->getByIds($id)->first();
    }

    /**
     * Get multiple specific models by their ids.
     *
     * @param array $ids
     * @return QueryResources
     */
    public function getByIds($ids): QueryResources
    {
        $result = $this->baseQuery()->whereKey($ids);

        // TODO: when fetching multiple ids, a subset might fail, so we have to compare count(ids) vs count(results)
        if (!$result) {
            throw new RepositoryException('No ' . $this->model . ' found with Ids: ' . $ids);
        }

        return new QueryResources($result);
    }

    /**
     * Persist model (changes) to the database.
     *
     * @param Model $model
     * @return Model
     */
    public function persist(Model $model): Model
    {
        $model->save();

        return $model;
    }

    /**
     * @return Builder
     */
    protected function baseQuery(): Builder
    {
        if (!$this->model) {
            throw new RepositoryException('Error no model set');
        }
        return $this->model::query();
    }

}
