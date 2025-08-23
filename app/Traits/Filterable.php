<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Filterable
{
    protected $filterOperators = [
        'eq' => '=',
        'ne' => '!=',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'like' => 'like',
        'in' => 'in',
        'nin' => 'not in',
        'null' => 'null',
        'notnull' => 'not null',
    ];

    public function scopeFilter(Builder $query, Request $request): Builder
    {
        $filters = $request->get('filters', []);

        foreach ($filters as $field => $filter) {
            $this->applyFilter($query, $field, $filter);
        }

        return $query;
    }

    protected function applyFilter(Builder $query, string $field, $filter): void
    {
        // Filtro simples: campo=valor
        if (is_string($filter)) {
            $query->where($field, $filter);
            return;
        }

        // Filtro avançado: [operator, value]
        if (is_array($filter)) {
            $operator = $filter['operator'] ?? 'eq';
            $value = $filter['value'] ?? null;

            if (array_key_exists($operator, $this->filterOperators)) {
                $dbOperator = $this->filterOperators[$operator];

                switch ($dbOperator) {
                    case 'like':
                        $query->where($field, 'like', "%$value%");
                        break;
                    case 'in':
                        $query->whereIn($field, (array)$value);
                        break;
                    case 'not in':
                        $query->whereNotIn($field, (array)$value);
                        break;
                    case 'null':
                        $query->whereNull($field);
                        break;
                    case 'not null':
                        $query->whereNotNull($field);
                        break;
                    default:
                        $query->where($field, $dbOperator, $value);
                }
            }
        }
    }

    public function scopeSearch(Builder $query, string $search, array $fields = []): Builder
    {
        if (empty($search) || empty($fields)) {
            return $query;
        }

        return $query->where(function ($q) use ($search, $fields) {
            foreach ($fields as $field) {
                $q->orWhere($field, 'like', "%{$search}%");
            }
        });
    }

    public function scopeSort(Builder $query, string $sortField, string $sortDirection = 'asc'): Builder
    {
        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? $sortDirection : 'asc';

        if ($this->isSortable($sortField)) {
            return $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }

    public function scopePaginateIntelligently(Builder $query, int $defaultPerPage = 15)
    {
        $perPage = request()->get('per_page', $defaultPerPage);
        $maxPerPage = config('app.max_per_page', 100);

        return $query->paginate(
            min($perPage, $maxPerPage)
        );
    }

    protected function isSortable(string $field): bool
    {
        $sortable = property_exists($this, 'sortable') ? $this->sortable : [];
        return in_array($field, $sortable) || in_array($field, $this->getFillable());
    }
}
