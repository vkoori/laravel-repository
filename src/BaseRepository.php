<?php

namespace Vkoori\Repository;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Vkoori\EntityDto\BaseDTO;

/**
 * @template TModel of Model
 * @template TDto of BaseDTO
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @return TModel
     */
    abstract protected function getModel(): Model;

    /**
     * @return TDto
     */
    abstract protected function getDTO(): BaseDTO;

    /**
     * @param BaseDTO $attributes
     * @return TDto
     */
    public function create(BaseDTO $attributes): BaseDTO
    {
        $model = $this->getModel()->create($attributes->toArray());
        return $this->getDTO()::fromModel($model);
    }

    /**
     * @param BaseDTO|null $conditions
     * @param array $relations
     * @return Collection<TDTO>
     */
    public function get(?BaseDTO $conditions = null, array $relations = []): Collection
    {
        return $this->fetchData($conditions, $relations)
            ->get()
            ->map(fn($model) => $this->getDTO()::fromModel($model));
    }

    /**
     * @param BaseDTO|null $conditions
     * @param array $relations
     * @param int|null $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(?BaseDTO $conditions = null, array $relations = [], ?int $perPage = null): LengthAwarePaginator
    {
        $paginator = $this->fetchData($conditions, $relations)->paginate($perPage);

        $paginator->getCollection()->transform(fn($model) => $this->getDTO()::fromModel($model));

        return $paginator;
    }

    /**
     * @param int $modelId
     * @param array $relations
     * @return TDto|null
     */
    public function findById(int $modelId, array $relations = []): ?BaseDTO
    {
        $model = $this->getModel()->query()->with($relations)->find($modelId);
        return $model ? $this->getDTO()::fromModel($model) : null;
    }

    /**
     * @param int $modelId
     * @param array $relations
     * @return TDto
     */
    public function findByIdOrFail(int $modelId, array $relations = []): BaseDTO
    {
        $model = $this->getModel()->query()->with($relations)->findOrFail($modelId);
        return $this->getDTO()::fromModel($model);
    }

    /**
     * @param BaseDTO|null $conditions
     * @param array $relations
     * @return TDto|null
     */
    public function first(?BaseDTO $conditions = null, array $relations = []): ?BaseDTO
    {
        $model = $this->fetchData($conditions, $relations)->first();
        return $model ? $this->getDTO()::fromModel($model) : null;
    }

    /**
     * @param BaseDTO|null $conditions
     * @param array $relations
     * @return TDto
     */
    public function firstOrFail(?BaseDTO $conditions = null, array $relations = []): BaseDTO
    {
        $model = $this->fetchData($conditions, $relations)->firstOrFail();
        return $this->getDTO()::fromModel($model);
    }

    public function count(?BaseDTO $conditions = null): int
    {
        return $this->fetchData($conditions, [])->count();
    }

    public function exists(?BaseDTO $conditions = null): bool
    {
        return $this->fetchData($conditions, [])->exists();
    }

    /**
     * @param int $modelId
     * @param BaseDTO $values
     * @return TDto
     */
    public function update(int $modelId, BaseDTO $values): BaseDTO
    {
        $model = $this->getModel()->findOrFail($modelId);
        $model->fill($values->toArray());
        $model->saveOrFail();

        return $this->getDTO()::fromModel($model);
    }

    /**
     * @param BaseDTO $attributes
     * @param BaseDTO $values
     * @return TDto
     */
    public function firstOrCreate(BaseDTO $attributes, BaseDTO $values): BaseDTO
    {
        $model = $this->getModel()->query()->firstOrCreate(
            $attributes->toArray(),
            $values->toArray()
        );
        return $this->getDTO()::fromModel($model);
    }

    /**
     * @param BaseDTO $attributes
     * @param BaseDTO $values
     * @return TDto
     */
    public function updateOrCreate(BaseDTO $attributes, BaseDTO $values): BaseDTO
    {
        $model = $this->getModel()->query()->updateOrCreate(
            $attributes->toArray(),
            $values->toArray()
        );
        return $this->getDTO()::fromModel($model);
    }

    public function deleteById(int $modelId): bool
    {
        return $this->getModel()->findOrFail($modelId)->delete();
    }

    /**
     * @param array<BaseDTO> $values
     */
    public function batchInsert(array $values): bool
    {
        if ($this->getModel()->timestamps) {
            $now = Carbon::now();
            foreach ($values as &$value) {
                $value = $value->toArray();
                if ($this->getModel()->getCreatedAtColumn()) {
                    $value[$this->getModel()->getCreatedAtColumn()] = $now;
                }
                if ($this->getModel()->getUpdatedAtColumn()) {
                    $value[$this->getModel()->getUpdatedAtColumn()] = $now;
                }
            }
        }

        return $this->getModel()->insert($values);
    }

    public function batchUpdate(BaseDTO $conditions, BaseDTO $values): int
    {
        return $this->fetchData($conditions, [])->update($values->toArray());
    }

    public function batchDelete(BaseDTO $conditions): int
    {
        return $this->fetchData($conditions, [])->delete();
    }

    private function fetchData(?BaseDTO $conditions, array $relations)
    {
        return $this
            ->getModel()
            ->query()
            ->when(
                $conditions,
                fn($query) => $query->where($conditions->toArray())
            )
            ->with($relations);
    }
}
