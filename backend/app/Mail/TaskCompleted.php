<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject('Task Completed: ' . $this->task->title)
                    ->view('emails.task-completed')
                    ->with([
                        'task' => $this->task,
                        'project' => $this->task->project,
                        'completedBy' => $this->task->assignedUser
                    ]);
    }
}