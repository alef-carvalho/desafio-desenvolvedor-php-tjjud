<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(<<<SQL
            CREATE OR REPLACE VIEW book_top_authors_view AS
            SELECT
                authors.id AS author_id,
                authors.name AS author_name,
                COUNT(DISTINCT author_book.book_id) AS books_count
            FROM authors
            INNER JOIN author_book ON author_book.author_id = authors.id
            INNER JOIN books ON books.id = author_book.book_id
            WHERE books.deleted_at IS NULL
            GROUP BY authors.id, authors.name
            ORDER BY books_count DESC;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_top_authors_view');
    }
};
