<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookSubject extends Pivot
{
    protected $fillable = ['book_id', 'subject_id'];
}
