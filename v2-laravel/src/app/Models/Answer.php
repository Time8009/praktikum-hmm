<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'tbl_answer';
    protected $primaryKey = 'answer_id';
    public $timestamps = true;

    protected $fillable = [
        'form_id',
        'user_id',
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function formular(): BelongsTo
    {
        return $this->belongsTo(Formular::class, 'form_id', 'form_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'response_id', 'answer_id');
    }
}