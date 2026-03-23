<?php

namespace Modules\Worship\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorshipRoster extends Model
{
    use HasFactory;

    protected $fillable = [
        'setlist_id',
        'user_id',
        'instrument_id',
        'worship_team_role_id',
        'status',
        'member_notes',
        'notified_at',
        'responded_at',
    ];

    protected $casts = [
        'status' => \Modules\Worship\App\Enums\RosterStatus::class,
        'notified_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function setlist()
    {
        return $this->belongsTo(WorshipSetlist::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instrument()
    {
        return $this->belongsTo(WorshipInstrument::class);
    }

    public function worshipTeamRole()
    {
        return $this->belongsTo(WorshipTeamRole::class);
    }
}
