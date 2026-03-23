<?php

namespace Modules\Sermons\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Sermons\App\Models\Sermon;

/**
 * Serviço central da API de sermões (v1).
 */
class SermonApiService
{
    /**
     * Lista sermões (paginado). Filtros: status, visibility, category_id, series_id, featured.
     *
     * @return LengthAwarePaginator<Sermon>
     */
    public function list(
        int $perPage = 15,
        ?string $status = null,
        ?string $visibility = null,
        ?int $categoryId = null,
        ?int $seriesId = null,
        ?bool $featuredOnly = null
    ): LengthAwarePaginator {
        $query = Sermon::with(['category', 'series', 'user'])->latest('sermon_date');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        if ($visibility !== null && $visibility !== '') {
            $query->where('visibility', $visibility);
        }
        if ($categoryId !== null) {
            $query->where('category_id', $categoryId);
        }
        if ($seriesId !== null) {
            $query->where('series_id', $seriesId);
        }
        if ($featuredOnly === true) {
            $query->where('is_featured', true);
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca sermão por id.
     */
    public function getById(int $id): ?Sermon
    {
        return Sermon::with(['category', 'series', 'user', 'tags', 'bibleReferences'])->find($id);
    }

    /**
     * Busca sermão por slug.
     */
    public function getBySlug(string $slug): ?Sermon
    {
        return Sermon::with(['category', 'series', 'user', 'tags', 'bibleReferences'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Cria sermão (aceita bible_references e tags).
     */
    public function create(array $data): Sermon
    {
        $refs = $data['bible_references'] ?? null;
        $tags = $data['tags'] ?? null;
        unset($data['bible_references'], $data['tags']);

        $sermon = Sermon::create($data);

        if (is_array($tags) && $tags !== []) {
            $this->syncTags($sermon, $tags);
        }
        if (is_array($refs) && $refs !== []) {
            $this->syncBibleReferences($sermon, $refs);
        }

        return $sermon->fresh(['category', 'series', 'user', 'tags', 'bibleReferences']);
    }

    /**
     * Atualiza sermão (aceita bible_references e tags).
     */
    public function update(Sermon $sermon, array $data): Sermon
    {
        $refs = $data['bible_references'] ?? null;
        $tags = $data['tags'] ?? null;
        unset($data['bible_references'], $data['tags']);

        $sermon->update($data);

        if (is_array($tags)) {
            $this->syncTags($sermon, $tags);
        }
        if (is_array($refs)) {
            $sermon->bibleReferences()->delete();
            $this->syncBibleReferences($sermon, $refs);
        }

        return $sermon->fresh(['category', 'series', 'user', 'tags', 'bibleReferences']);
    }

    private function syncTags(Sermon $sermon, array $tags): void
    {
        $ids = [];
        foreach ($tags as $t) {
            if (is_numeric($t)) {
                $ids[] = (int) $t;
            } else {
                $tag = \Modules\Sermons\App\Models\SermonTag::firstOrCreate(
                    ['name' => $t],
                    ['slug' => \Illuminate\Support\Str::slug($t)]
                );
                $ids[] = $tag->id;
            }
        }
        $sermon->tags()->sync(array_unique($ids));
    }

    private function syncBibleReferences(Sermon $sermon, array $refs): void
    {
        foreach ($refs as $i => $ref) {
            if (empty($ref['book'] ?? null)) {
                continue;
            }
            $sermon->bibleReferences()->create([
                'book' => $ref['book'],
                'chapter' => $ref['chapter'] ?? null,
                'verses' => $ref['verses'] ?? null,
                'reference_text' => $ref['reference_text'] ?? null,
                'bible_version_id' => $ref['bible_version_id'] ?? null,
                'book_id' => $ref['book_id'] ?? null,
                'chapter_id' => $ref['chapter_id'] ?? null,
                'type' => $ref['type'] ?? 'main',
                'context' => $ref['context'] ?? null,
                'order' => $i,
            ]);
        }
    }

    /**
     * Exclui sermão (soft delete).
     */
    public function destroy(Sermon $sermon): bool
    {
        return $sermon->delete();
    }
}
