<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Department
 *
 * @property string $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */

class Department extends Model
{
    protected $connection = 'sso_mysql';

    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
