<?php

namespace Src\Server\Database\Eloquent\Traits;

trait QueryTrait
{
    /**
     * Execute the query and get the first result.
     *
     * @param  array  $columns
     * @return \Src\Server\Database\Eloquent\Model|object|static|null
     */
    public function first($columns = ['*'])
    {
        return $this->get($columns)->first();
    }
}