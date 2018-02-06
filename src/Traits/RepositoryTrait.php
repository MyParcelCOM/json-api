<?php declare(strict_types=1);

namespace MyParcelCom\JsonApi\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MyParcelCom\JsonApi\Exceptions\RepositoryException;
use MyParcelCom\JsonApi\Resources\QueryResources;

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
            throw new RepositoryException('No model found with ids ' . implode(', ', $ids));
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

    /**
     * @param Model $model
     * @return $this
     */
    public function delete(Model $model): self
    {
        $model->delete();

        return $this;
    }
}
