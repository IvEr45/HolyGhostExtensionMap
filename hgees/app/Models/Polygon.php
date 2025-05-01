<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polygon extends Model
{
    use HasFactory;

    protected $fillable = ['coordinates', 'house_number', 'residents', 'color'];


    /**
     * Get the polygon coordinates as an array.
     *
     * @return array
     */
    public function getCoordinatesArrayAttribute()
    {
        return json_decode($this->coordinates, true);
    }
}
