<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states'; // Explicitly set the table name
    public $timestamps = false;  // Disable timestamps if your table doesn't have created_at and updated_at
    protected $fillable = ['state']; //optional

    public static function getStatesDropdown()
    {
        $states = self::all(); // Fetch all states
        $options = [];
        foreach ($states as $state) {
            $options[$state->id] = $state->state; // Create an array of id => state name
        }
        return $options;
    }
}
