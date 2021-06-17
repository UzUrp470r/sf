<?php

namespace App\Repository;

use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface PaginatorRepositoryInterface
{
    /**
     * Get the repository pagination
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param array $data
     *
     * @return PaginationInterface
     */
    public function getPagination(Request $request, PaginatorInterface $paginator, array $data = []): PaginationInterface;
}