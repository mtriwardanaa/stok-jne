<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Group
 *
 * @property string $id
 * @property string $name
 * @property string $partner_id
 * @property string $region_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Partner $partner
 * @property Region $region
 *
 * @package App\Models
 */

class Group extends Model
{
    protected $connection = 'sso_mysql';
    protected $table = 'groups';

    protected $fillable = [
        'name',
        'partner_id',
        'region_id',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('database.connections.sso_mysql.database') . '.' . $this->table;
        parent::__construct($attributes);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }
}
