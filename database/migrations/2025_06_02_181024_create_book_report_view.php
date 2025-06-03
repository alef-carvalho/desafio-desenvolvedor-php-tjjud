<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(<<<SQL
        CREATE OR REPLACE VIEW book_report_view AS
        SELECT
            books.id AS book_id,
            books.title AS livro,
            books.edition AS edicao,
            books.publisher AS editora,
            books.published_year AS ano,
            GROUP_CONCAT(DISTINCT authors.name ORDER BY authors.name SEPARATOR ', ') AS autores,
            GROUP_CONCAT(DISTINCT subjects.description ORDER BY subjects.description SEPARATOR ', ') AS assuntos
        FROM books
        LEFT JOIN author_book ON author_book.book_id = books.id
        LEFT JOIN authors ON authors.id = author_book.author_id
        LEFT JOIN book_subject ON book_subject.book_id = books.id
        LEFT JOIN subjects ON subjects.id = book_subject.subject_id
        WHERE books.deleted_at IS NULL
        GROUP BY books.id, books.title, books.edition, books.publisher, books.published_year;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_report_view');
    }
};
