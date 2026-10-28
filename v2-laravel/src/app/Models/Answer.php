<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'tbl_answer';
    protected $primaryKey = 'answer_id';
    public $timestamps = true;

    protected $fillable = [
        'form_id',
        'message',
    ];

    public static function validationRules(): array
    {
        return [
            'form_id' => 'required|integer|exists:tbl_formular,form_id',
            'message' => 'required|string|min:1',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'message' => 'required|string|min:1',
        ];
    }

    public function formular(): BelongsTo
    {
        return $this->belongsTo(Formular::class, 'form_id', 'form_id');
    }
}