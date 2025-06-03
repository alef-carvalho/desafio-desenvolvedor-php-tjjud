<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Collection\Collection;

/**
 * @property int $id
 * @property string $title
 * @property string $edition
 * @property string $publisher
 * @property int $published_year
 * @property Collection $authors
 * @property Collection $subjects
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Book extends Model
{

    use SoftDeletes;

    protected $fillable = [
        "title",
        "edition",
        "publisher",
        "published_year"
    ];

    protected $casts = [
        "edition" => "integer",
    ];

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class)->using(AuthorBook::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)->using(BookSubject::class);
    }
}
