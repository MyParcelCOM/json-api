<?php declare(strict_types=1);

namespace MyParcelCom\PostNl\Repository\Traits;

use Illuminate\Database\Eloquent\Model;
use MyParcelCom\Common\Contracts\ResultSetInterface;
use MyParcelCom\Common\Exceptions\RepositoryException;
use MyParcelCom\Common\ResultSets\QueryResultSet;

/**
 * Repository generic functions
 */
trait EloquentRepositoryTrait
{
    /**
     * Get ALL from this model.
     *
     * @return ResultSetInterface
     * @throws RepositoryException
     */
    public function getAll(): ResultSetInterface
    {
        if (!$this->model) {
            throw new RepositoryException('Error no model set');
        }
        $query = $this->model::query();

        return new QueryResultSet($query);
    }

    /**
     * Get a specific model by it's id.
     *
     * @param string $id
     * @return Model
     * @throws RepositoryException
     */
    public function getById(string $id)
    {
        if (!$this->model) {
            throw new RepositoryException('Error no model set');
        }

        $result = $this->model::where('id', $id)->first();

        if (!$result) {
            throw new RepositoryException('No ' . $this->model . ' found with Id: ' . $id);
        }

        return $result;
    }

    /**
     * Get multiple specific models by their ids.
     *
     * @param array $ids
     * @return ResultSetInterface
     * @throws RepositoryException
     */
    public function getByIds(array $ids): ResultSetInterface
    {
        if (!$this->model) {
            throw new RepositoryException('Error no model set');
        }

        $query = $this->model::whereIn('id', $ids);

        return new QueryResultSet($query);
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
}
