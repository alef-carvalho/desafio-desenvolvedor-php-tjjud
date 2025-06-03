<?php

namespace App\Http\DTO\Pagination;

class PaginationDTO
{
    public function __construct(
        public array $data,
        public PaginationMetadataDTO $meta
    ) {
    }
}
