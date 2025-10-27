<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    // protected is the correct keyword for defining model properties
    protected $table = "case_status"; 
    protected $fillable = ['parent_id', 'name', 'type', 'status', 'created_by', 'updated_by'];
}
