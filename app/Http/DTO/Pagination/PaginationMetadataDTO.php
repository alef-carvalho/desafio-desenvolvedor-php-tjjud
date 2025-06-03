<?php

namespace App\Http\DTO\Pagination;

class PaginationMetadataDTO
{
    public function __construct(
        public int $page,
        public int $size,
        public int $totalPages,
        public int $totalResults,
    ) {
    }
}
