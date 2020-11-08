<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $casts = [
        'show_members_on_roster' => 'boolean',
        'show_role'              => 'boolean',
        'is_admin'               => 'boolean',
    ];
}
