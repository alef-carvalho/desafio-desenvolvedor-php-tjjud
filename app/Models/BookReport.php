<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookReport extends Model
{
    protected $table = 'book_report_view';

    protected $primaryKey = 'book_id';

    public $timestamps = false;

    protected $guarded = [];
}
