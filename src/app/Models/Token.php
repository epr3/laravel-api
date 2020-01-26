<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;

class Token extends Model
{
    use GeneratesUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'token',
        'type',
        'expires_at',
        'user_id',
    ];

    public function uuidColumn()
    {
        return 'id';
    }
}
