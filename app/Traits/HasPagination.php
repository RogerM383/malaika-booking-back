<?php

namespace App\Traits;

trait HasPagination
{
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
