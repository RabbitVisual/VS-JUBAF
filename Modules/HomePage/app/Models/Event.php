<?php

namespace Modules\HomePage\App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'image',
        'is_active',
        'order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_by' => 'integer',
    ];

    /**
     * Scope para eventos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para eventos futuros
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', Carbon::now())
            ->orderBy('start_date');
    }

    /**
     * Scope para ordenação
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('start_date');
    }

    /**
     * Verifica se o evento é hoje
     */
    public function isToday()
    {
        return $this->start_date->isToday();
    }

    /**
     * Verifica se o evento é amanhã
     */
    public function isTomorrow()
    {
        return $this->start_date->isTomorrow();
    }

    /**
     * Formatar data para exibição
     */
    public function getFormattedDateAttribute()
    {
        if ($this->isToday()) {
            return 'Hoje';
        } elseif ($this->isTomorrow()) {
            return 'Amanhã';
        } else {
            return $this->start_date->format('d/m/Y');
        }
    }

    /**
     * Formatar horário para exibição
     */
    public function getFormattedTimeAttribute()
    {
        if ($this->start_date && $this->end_date) {
            $startTime = $this->start_date->format('H:i');
            $endTime = $this->end_date->format('H:i');

            return $startTime.' - '.$endTime;
        } elseif ($this->start_date) {
            return $this->start_date->format('H:i');
        }

        return '';
    }
}
