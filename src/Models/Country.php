<?php


namespace Iserter\World\Models;


use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $casts = ['language_codes' => 'array'];
}
