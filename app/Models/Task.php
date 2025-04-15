<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'isComplete',
        'isArchived',
        'priority_level' // Include this if you've added the isArchived column
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'isComplete' => 'boolean',
        'isArchived' => 'boolean', // Include this if isArchived is a boolean
    ];
}