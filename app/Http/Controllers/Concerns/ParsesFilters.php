<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ParsesFilters
{
    protected function parseFilters(?string $filters): array
    {
        $result = [];
        if (!$filters) {
            return $result;
        }

        foreach (explode(',', $filters) as $part) {
            $part = trim($part);
            if ($part === '') continue;
            if (!preg_match('/^(\w+)(==|>=|<=|>|<)(.+)$/', $part, $m)) continue;
            $field = $m[1];
            $op = $m[2];
            $raw = trim($m[3]);

            $val = $this->castFilterValue($raw);
            $result[] = [$field, $op, $val];
        }
        return $result;
    }

    protected function castFilterValue(string $raw): mixed
    {
        $v = strtolower($raw);
        if ($v === 'true') return true;
        if ($v === 'false') return false;
        if (is_numeric($raw)) {
            return str_contains($raw, '.') ? (float) $raw : (int) $raw;
        }
        return trim($raw, "'\"");
    }

    protected function applyBasicFilters(Builder $q, array $filters, array $map = [], bool $softDeletes = false): void
    {
        foreach ($filters as [$field, $op, $value]) {
            $column = $map[$field] ?? $field;
            if ($field === 'deleted' && $softDeletes) {
                // Laravel handles soft deletes via scopes
                if ($value === true) {
                    $q->onlyTrashed();
                } else {
                    $q->withoutTrashed();
                }
                continue;
            }

            switch ($op) {
                case '==':
                    $q->where($column, $value);
                    break;
                case '>':
                    $q->where($column, '>', $value);
                    break;
                case '>=':
                    $q->where($column, '>=', $value);
                    break;
                case '<':
                    $q->where($column, '<', $value);
                    break;
                case '<=':
                    $q->where($column, '<=', $value);
                    break;
            }
        }
    }

    protected function toQueryResult(LengthAwarePaginator $paginator): array
    {
        return [
            'items' => $paginator->items(),
            'total' => $paginator->total(),
        ];
    }
}

