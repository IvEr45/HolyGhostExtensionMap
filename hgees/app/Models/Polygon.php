<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polygon extends Model
{
    use HasFactory;

    protected $fillable = ['coordinates'];

    // No need for array casting as coordinates is stored as a longText string
    // If you want to use as an array in your code, you can create accessor methods
    
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