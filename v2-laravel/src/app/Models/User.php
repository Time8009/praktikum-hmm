<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'tbl_user';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'email',
        'birthdate',
        'adress',
        'zip_code',
        'city',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    public static function validationRules(): array
    {
        return [
            'first_name' => 'required|string|min:2|max:50',
            'last_name'  => 'nullable|string|min:2|max:50',
            'role'       => 'sometimes|in:A,U',
            'email'      => 'required|email|max:100',
            'birthdate'  => 'nullable|date',
            'adress'     => 'nullable|string|max:100',
            'zip_code'   => 'nullable|string|max:5',
            'city'       => 'nullable|string|max:100',
        ];
    }

    public function formulars(): HasMany
    {
        return $this->hasMany(Formular::class, 'user_id', 'id');
    }
}