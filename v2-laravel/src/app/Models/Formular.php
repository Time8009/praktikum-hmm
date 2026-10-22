<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formular extends Model
{
    protected $table = 'tbl_formular';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'birthdate',
        'email',
        'address',
        'zip_code',
        'city',
        'subject',
        'message',
    ];
}