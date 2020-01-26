<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Dyrynda\Database\Support\GeneratesUuid;

class Role extends Model
{
    use GeneratesUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name'
    ];

    public function uuidColumn()
    {
        return 'id';
    }
}
