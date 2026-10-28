<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Formular extends Model
{
    protected $table = 'tbl_formular';
    protected $primaryKey = 'form_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'subject_id',
        'message',
    ];

    public static function validationRules(): array
    {
        return [
            'id'      => 'nullable|integer',
            'oid'     => 'nullable|integer',
            'message' => 'required|string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function answers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Answer::class, 'form_id', 'form_id');
    }
}