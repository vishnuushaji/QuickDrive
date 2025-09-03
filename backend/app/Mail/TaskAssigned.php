<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject('New Task Assigned: ' . $this->task->title)
                    ->view('emails.task-assigned')
                    ->with([
                        'task' => $this->task,
                        'project' => $this->task->project,
                        'assignedUser' => $this->task->assignedUser
                    ]);
    }
}