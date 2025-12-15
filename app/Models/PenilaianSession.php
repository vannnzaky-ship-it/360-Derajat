<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenilaianSession extends Model
{
    protected $guarded = ['id'];
    protected $dates = ['batas_waktu', 'tanggal_mulai'];

    public function siklus(): BelongsTo
    {
        return $this->belongsTo(Siklus::class);
    }

    public function alokasis(): HasMany
    {
        return $this->hasMany(PenilaianAlokasi::class);
    }
}