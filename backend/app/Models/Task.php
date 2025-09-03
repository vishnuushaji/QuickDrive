<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'assigned_user_id',
        'title',
        'description',
        'attachment',
        'status',
        'priority',
        'start_date',
        'due_date',
        'hours',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    public function developers()
{
    // many-to-many relationship
    return $this->belongsToMany(User::class, 'task_user', 'task_id', 'user_id');
}
}