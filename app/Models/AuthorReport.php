<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorReport extends Model
{
    protected $table = 'book_top_authors_view';

    protected $primaryKey = 'book_id';

    public $timestamps = false;

    protected $guarded = [];
}
