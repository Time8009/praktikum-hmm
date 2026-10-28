<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $table = 'tbl_subject';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'note',
    ];

    public static function validationRules(): array
    {
        return [
            'subject' => 'required|integer|exists:tbl_subject,id',
        ];
    }

    public function formulars(): HasMany
    {
        return $this->hasMany(Formular::class, 'subject_id', 'id');
    }
}