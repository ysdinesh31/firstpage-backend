<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $fillable = ['title', 'description', 'assignedTo', 'due_date', 'status', 'created_by', 'deleted_by'];

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    // public function createdBy()
    // {
    //     return $this->belongsTo('User', 'created_by');
    // }

    public function assignedto()
    {
        return $this->belongsTo('App\User', 'assignedTo');
    }

    public function createdby()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function deletedby()
    {
        return $this->belongsTo('App\User', 'deleted_by');
    }
}
