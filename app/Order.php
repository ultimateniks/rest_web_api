<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    const UNASSIGNED_ORDER_STATUS = 'UNASSIGNED';
    const ASSIGNED_ORDER_STATUS = 'TAKEN';

    public function getDistanceValue()
    {
        return $this->distance_value;
    }
}
