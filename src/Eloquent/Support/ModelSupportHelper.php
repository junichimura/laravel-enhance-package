<?php

namespace Junichimura\LaravelEnhancePackage\Eloquent\Support;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Trait ModelSupportHelper
 *
 * @package App
 *
 * @method static $this find($id, $columns = ['*'])
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder whereColumn($first, $operator = null, $second = null, $boolean = 'and')
 * @method static Builder whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder whereNotIn($column, $values, $boolean = 'and')
 * @method static Builder orderBy($column, $direction = 'asc')
 * @method static Builder orderByDesc($column)
 * @method static Collection get($columns = ['*'])
 * @method static $this first($columns = ['*'])
 * @method static LengthAwarePaginator paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null)
 */
trait ModelSupportHelper
{

}