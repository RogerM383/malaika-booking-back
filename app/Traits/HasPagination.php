<?php

namespace App\Traits;

trait HasPagination
{
    protected int $defaultPage = 1;
    protected int $defaultPerPage = 10;

    /**
     * @param $per_page
     * @param $page
     * @return bool
     */
    public function isPaginated($per_page = null, $page = null): bool
    {
        return isset($per_page) || isset($page);
    }
}
