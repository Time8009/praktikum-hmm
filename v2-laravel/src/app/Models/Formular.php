<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formular extends Model
{
    use HasFactory;

    protected $table = 'tbl_formular';
    protected $primaryKey = 'form_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'subject_id',
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'form_id', 'form_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'form_id', 'form_id');
    }
}