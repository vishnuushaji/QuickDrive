<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'progress',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function clients()
    {
        return $this->users()->wherePivot('role', 'client');
    }

    public function developers()
    {
        return $this->users()->wherePivot('role', 'developer');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Calculate progress based on approved tasks
    public function calculateProgress()
    {
        if (!$this->relationLoaded('tasks')) {
            $tasks = $this->tasks()->get();
        } else {
            $tasks = $this->tasks;
        }

        $totalTasks = $tasks->count();
        if ($totalTasks === 0) return 0;

        $approvedTasks = $tasks->where('status', 'approved')->count();
        return round(($approvedTasks / $totalTasks) * 100, 2);
    }
}