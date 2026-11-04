<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
{
    use HasFactory;

    protected $table = 'tbl_file';
    protected $primaryKey = 'file_id';

    protected $fillable = [
        'form_id',
        'response_id',
        'user_id',
        'name',
        'path',
        'disk',
        'mime_type',
        'size_in_bytes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function formular(): BelongsTo
    {
        return $this->belongsTo(Formular::class, 'form_id', 'form_id');
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'response_id', 'answer_id');
    }
}