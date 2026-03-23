<?php

namespace Modules\Admin\App\Services;

use App\Models\UserRelationship;
use Illuminate\Support\Collection;

/**
 * Analisa user_relationships (aceitos) para identificar núcleos familiares,
 * composição (família completa, monoparental, casal, individual) e distribuição etária.
 * Otimizado para evitar N+1: uma carga de relacionamentos e usuários necessários.
 */
class FamilyAnalysisService
{
    /** @var array<int, array{id: int, name: string, date_of_birth: ?string, neighborhood: ?string, city: ?string}> */
    protected array $userCache = [];

    /** @var Collection<int, UserRelationship> */
    protected Collection $acceptedRelationships;

    public function __construct()
    {
        $this->acceptedRelationships = UserRelationship::query()
            ->accepted()
            ->with(['user:id,name,date_of_birth,neighborhood,city', 'relatedUser:id,name,date_of_birth,neighborhood,city'])
            ->get();
        foreach ($this->acceptedRelationships as $rel) {
            if ($rel->user) {
                $this->userCache[$rel->user_id] = [
                    'id' => $rel->user->id,
                    'name' => $rel->user->name,
                    'date_of_birth' => $rel->user->date_of_birth?->format('Y-m-d'),
                    'neighborhood' => $rel->user->neighborhood,
                    'city' => $rel->user->city,
                ];
            }
            if ($rel->related_user_id && $rel->relatedUser) {
                $this->userCache[$rel->related_user_id] = [
                    'id' => $rel->relatedUser->id,
                    'name' => $rel->relatedUser->name,
                    'date_of_birth' => $rel->relatedUser->date_of_birth?->format('Y-m-d'),
                    'neighborhood' => $rel->relatedUser->neighborhood,
                    'city' => $rel->relatedUser->city,
                ];
            }
        }
    }

    /**
     * Retorna estatísticas completas para o dashboard demográfico.
     */
    public function getDemographicsReport(): array
    {
        $nuclei = $this->buildNuclei();
        $composition = $this->computeComposition($nuclei);
        $byNeighborhood = $this->familiesByNeighborhood($nuclei);
        $highlights = $this->buildPastoralHighlights($nuclei, $composition);
        $ageDistribution = $this->averageChildrenAgeByNucleus($nuclei);

        return [
            'nuclei' => $nuclei,
            'composition' => $composition,
            'by_neighborhood' => $byNeighborhood,
            'pastoral_highlights' => $highlights,
            'age_distribution' => $ageDistribution,
            'total_users_with_relations' => $this->acceptedRelationships->pluck('user_id')->merge($this->acceptedRelationships->pluck('related_user_id'))->filter()->unique()->count(),
            'total_relationships' => $this->acceptedRelationships->count(),
        ];
    }

    /**
     * Núcleos: agrupa por casal (cônjuge) e por pai/mãe + filhos.
     * Cada núcleo é um array com keys: type (couple|single_parent|individual), member_ids, spouse_pair, parent_ids, child_ids.
     */
    protected function buildNuclei(): array
    {
        $couplePairs = []; // (id_min, id_max) => [id1, id2]
        $parentToChildren = []; // parent_id => [child_id, ...]
        $childToParents = [];  // child_id => [parent_id, ...]

        foreach ($this->acceptedRelationships as $rel) {
            $u = $rel->user_id;
            $r = $rel->related_user_id;
            if (! $r) {
                continue;
            }
            if ($rel->relationship_type === UserRelationship::TYPE_CONJUGE) {
                $key = [min($u, $r), max($u, $r)];
                $couplePairs[json_encode($key)] = [$u, $r];
            }
            if ($rel->relationship_type === UserRelationship::TYPE_FILHO) {
                $parentToChildren[$u] = array_merge($parentToChildren[$u] ?? [], [$r]);
                $childToParents[$r] = array_merge($childToParents[$r] ?? [], [$u]);
            }
            if (in_array($rel->relationship_type, [UserRelationship::TYPE_PAI, UserRelationship::TYPE_MAE], true)) {
                $childToParents[$u] = array_merge($childToParents[$u] ?? [], [$r]);
                $parentToChildren[$r] = array_merge($parentToChildren[$r] ?? [], [$u]);
            }
        }

        $assigned = []; // user_id => true se já está em algum núcleo
        $nuclei = [];

        // 1) Núcleos com casal (cônjuge) + eventualmente filhos
        foreach ($couplePairs as $pair) {
            $childIds = array_values(array_unique(array_merge(
                $parentToChildren[$pair[0]] ?? [],
                $parentToChildren[$pair[1]] ?? [],
                isset($childToParents[$pair[0]]) ? $childToParents[$pair[0]] : [],
                isset($childToParents[$pair[1]]) ? $childToParents[$pair[1]] : []
            )));
            $memberIds = array_values(array_unique(array_merge($pair, $childIds)));
            foreach ($memberIds as $id) {
                $assigned[$id] = true;
            }
            $nuclei[] = [
                'type' => 'couple',
                'member_ids' => $memberIds,
                'spouse_pair' => $pair,
                'parent_ids' => $pair,
                'child_ids' => $childIds,
            ];
        }

        // 2) Núcleos monoparentais: pais que têm filhos e não estão em casal já contado
        foreach ($parentToChildren as $parentId => $childIds) {
            if (isset($assigned[$parentId])) {
                continue;
            }
            $all = array_unique(array_merge([$parentId], $childIds));
            foreach ($all as $id) {
                $assigned[$id] = true;
            }
            $nuclei[] = [
                'type' => 'single_parent',
                'member_ids' => array_values($all),
                'spouse_pair' => null,
                'parent_ids' => [$parentId],
                'child_ids' => array_values($childIds),
            ];
        }

        // 3) Individuais: usuários que aparecem em relacionamentos mas não foram atribuídos a núcleo (ex.: só irmão)
        $allRelatedUserIds = $this->acceptedRelationships->pluck('user_id')->merge($this->acceptedRelationships->pluck('related_user_id'))->filter()->unique()->values()->all();
        foreach ($allRelatedUserIds as $uid) {
            if (! isset($assigned[$uid])) {
                $nuclei[] = [
                    'type' => 'individual',
                    'member_ids' => [$uid],
                    'spouse_pair' => null,
                    'parent_ids' => [],
                    'child_ids' => [],
                ];
            }
        }

        return $nuclei;
    }

    /**
     * Composição: conta famílias completas, monoparentais, casais e membros individuais (percentuais).
     */
    protected function computeComposition(array $nuclei): array
    {
        $complete = 0;
        $monoparental = 0;
        $couple = 0;
        $individual = 0;

        foreach ($nuclei as $n) {
            if ($n['type'] === 'individual') {
                $individual++;
                continue;
            }
            if ($n['type'] === 'single_parent') {
                $monoparental++;
                continue;
            }
            if ($n['type'] === 'couple') {
                if (count($n['child_ids']) >= 1) {
                    $complete++;
                } else {
                    $couple++;
                }
            }
        }

        $total = $complete + $monoparental + $couple + $individual;
        $total = $total ?: 1;

        return [
            'complete_families' => $complete,
            'monoparental' => $monoparental,
            'couples' => $couple,
            'individuals' => $individual,
            'total_nuclei' => $total,
            'pct_complete' => round(100 * $complete / $total, 1),
            'pct_monoparental' => round(100 * $monoparental / $total, 1),
            'pct_couples' => round(100 * $couple / $total, 1),
            'pct_individuals' => round(100 * $individual / $total, 1),
        ];
    }

    /**
     * Famílias (núcleos não-individuais) por bairro ou cidade.
     */
    protected function familiesByNeighborhood(array $nuclei): array
    {
        $byArea = [];
        foreach ($nuclei as $n) {
            if ($n['type'] === 'individual') {
                continue;
            }
            $firstId = $n['member_ids'][0] ?? null;
            if (! $firstId) {
                continue;
            }
            $info = $this->userCache[$firstId] ?? null;
            $area = $info['neighborhood'] ?: ($info['city'] ?: 'Sem região');
            $byArea[$area] = ($byArea[$area] ?? 0) + 1;
        }
        ksort($byArea);

        return $byArea;
    }

    /**
     * Média de idade dos filhos por núcleo (apenas núcleos com filhos).
     */
    protected function averageChildrenAgeByNucleus(array $nuclei): array
    {
        $today = now();
        $out = [];
        foreach ($nuclei as $i => $n) {
            if (empty($n['child_ids'])) {
                continue;
            }
            $ages = [];
            foreach ($n['child_ids'] as $childId) {
                $info = $this->userCache[$childId] ?? null;
                if (! $info || empty($info['date_of_birth'])) {
                    continue;
                }
                $dob = \Carbon\Carbon::parse($info['date_of_birth']);
                $ages[] = $today->diffInYears($dob);
            }
            if ($ages !== []) {
                $out[] = [
                    'nucleus_index' => $i,
                    'member_ids' => $n['member_ids'],
                    'average_child_age' => round(array_sum($ages) / count($ages), 1),
                    'child_count' => count($ages),
                ];
            }
        }

        return $out;
    }

    /**
     * Destaques pastorais: frases prontas para o dashboard.
     */
    protected function buildPastoralHighlights(array $nuclei, array $composition): array
    {
        $highlights = [];
        $today = now();

        $familiesPreschool = 0;
        $familiesSchool = 0;
        $familiesTeens = 0;
        $individualCount = $composition['individuals'] ?? 0;
        $couplesWithKids = $composition['complete_families'] ?? 0;

        foreach ($nuclei as $n) {
            $hasPreschool = false;
            $hasSchool = false;
            $hasTeens = false;
            foreach ($n['child_ids'] ?? [] as $childId) {
                $info = $this->userCache[$childId] ?? null;
                if (! $info || empty($info['date_of_birth'])) {
                    continue;
                }
                $age = $today->diffInYears(\Carbon\Carbon::parse($info['date_of_birth']));
                if ($age <= 5) {
                    $hasPreschool = true;
                } elseif ($age <= 12) {
                    $hasSchool = true;
                } elseif ($age <= 17) {
                    $hasTeens = true;
                }
            }
            if ($hasPreschool) {
                $familiesPreschool++;
            }
            if ($hasSchool) {
                $familiesSchool++;
            }
            if ($hasTeens) {
                $familiesTeens++;
            }
        }

        if ($familiesPreschool > 0) {
            $highlights[] = "Temos {$familiesPreschool} famílias com filhos em idade pré-escolar (0 a 5 anos).";
        }
        if ($familiesSchool > 0) {
            $highlights[] = "{$familiesSchool} famílias com filhos em idade escolar (6 a 12 anos).";
        }
        if ($familiesTeens > 0) {
            $highlights[] = "{$familiesTeens} famílias com adolescentes (13 a 17 anos).";
        }
        if ($couplesWithKids > 0) {
            $highlights[] = "{$couplesWithKids} núcleos com casal e filhos (família completa).";
        }
        if ($individualCount > 0) {
            $highlights[] = "{$individualCount} membros cadastrados sem núcleo familiar (individual ou vínculos apenas como irmão/outro).";
        }

        if ($highlights === []) {
            $highlights[] = 'Cadastre vínculos de parentesco (pai, mãe, cônjuge, filho) para enriquecer os destaques pastorais.';
        }

        return $highlights;
    }

    /**
     * Resumo em texto/array para o Elias (contexto da análise pastoral).
     */
    public function getSummaryForElias(): array
    {
        $report = $this->getDemographicsReport();

        return [
            'composition' => $report['composition'],
            'by_neighborhood' => $report['by_neighborhood'],
            'pastoral_highlights' => $report['pastoral_highlights'],
            'total_nuclei' => $report['composition']['total_nuclei'],
            'total_relationships' => $report['total_relationships'],
        ];
    }
}
