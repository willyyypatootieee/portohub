<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
final class PortfolioRepository
{
    public function __construct(private PDO $database)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function getAll(): array
    {
        $statement = $this->database->query(
            'SELECT id, title, creator_name, category, image_filename, likes_count, views_count, is_featured
             FROM portfolio_items
             ORDER BY display_order ASC, id DESC'
        );

        return $statement->fetchAll();
    }

    /** @return array<int, string> */
    public function getCategories(): array
    {
        $statement = $this->database->query(
            'SELECT DISTINCT category
             FROM portfolio_items
             WHERE category <> ""
             ORDER BY category ASC'
        );

        return array_column($statement->fetchAll(), 'category');
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order
             FROM portfolio_items
             WHERE id = :id'
        );
        $statement->execute(['id' => $id]);

        $item = $statement->fetch();

        return $item === false ? null : $item;
    }

    /** @param array<string, mixed> $item */
    public function create(array $item): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO portfolio_items
                (title, creator_name, category, image_filename, likes_count, views_count, is_featured, display_order)
             VALUES
                (:title, :creator_name, :category, :image_filename, :likes_count, :views_count, :is_featured, :display_order)'
        );
        $statement->execute($item);
    }

    /** @param array<string, mixed> $item */
    public function update(int $id, array $item): void
    {
        $item['id'] = $id;

        $statement = $this->database->prepare(
            'UPDATE portfolio_items
             SET title = :title,
                 creator_name = :creator_name,
                 category = :category,
                 image_filename = :image_filename,
                 likes_count = :likes_count,
                 views_count = :views_count,
                 is_featured = :is_featured,
                 display_order = :display_order
             WHERE id = :id'
        );
        $statement->execute($item);
    }

    public function delete(int $id): void
    {
        $statement = $this->database->prepare('DELETE FROM portfolio_items WHERE id = :id');
        $statement->execute(['id' => $id]);
    }
}
