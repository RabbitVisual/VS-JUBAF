<?php

namespace App\Models\Relations;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Relação que sempre retorna coleção vazia (sem tocar em tabelas ausentes).
 * Usada quando o módulo Ministries não está migrado/instalado.
 */
class EmptyEloquentRelation extends Relation
{
    public function __construct(Model $parent)
    {
        $related = new class extends Model
        {
            protected $table = 'users';
        };
        $related->setTable($parent->getTable());
        $query = $related->newQuery()->whereRaw('0 = 1');

        parent::__construct($query, $parent);
    }

    public function addConstraints(): void {}

    public function addEagerConstraints(array $models): void {}

    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    public function match(array $models, EloquentCollection $results, $relation): array
    {
        return $this->initRelation($models, $relation);
    }

    public function getResults()
    {
        return $this->related->newCollection();
    }
}
