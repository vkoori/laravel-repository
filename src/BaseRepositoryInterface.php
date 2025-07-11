<?php

namespace Vkoori\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Vkoori\EntityDto\BaseDTO;

/**
 * @template TModel
 * @template TDTO
 */
interface BaseRepositoryInterface
{
    public function create(BaseDTO $attributes): BaseDTO;

    /**
     * @return Collection<TDTO>
     */
    public function get(?BaseDTO $conditions = null, array $relations = []): Collection;

    public function paginate(?BaseDTO $conditions = null, array $relations = [], ?int $perPage = null): LengthAwarePaginator;

    public function findById(int $modelId, array $relations = []): ?BaseDTO;

    public function findByIdOrFail(int $modelId, array $relations = []): BaseDTO;

    public function first(?BaseDTO $conditions = null, array $relations = []): ?BaseDTO;

    public function firstOrFail(?BaseDTO $conditions = null, array $relations = []): BaseDTO;

    public function count(?BaseDTO $conditions = null): int;

    public function exists(?BaseDTO $conditions = null): bool;

    public function update(int $modelId, BaseDTO $values): BaseDTO;

    public function firstOrCreate(BaseDTO $attributes, BaseDTO $values): BaseDTO;

    public function updateOrCreate(BaseDTO $attributes, BaseDTO $values): BaseDTO;

    public function deleteById(int $modelId): bool;

    /**
     * @param array<BaseDTO> $values
     */
    public function batchInsert(array $values): bool;

    public function batchUpdate(BaseDTO $conditions, BaseDTO $values): int;

    public function batchDelete(BaseDTO $conditions): int;
}
