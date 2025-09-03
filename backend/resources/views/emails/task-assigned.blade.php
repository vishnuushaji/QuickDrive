<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Task Assigned</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f4f4f4; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #fff; }
        .task-details { background-color: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Task Assigned</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>A new task has been assigned in the Quick Drive system.</p>
            
            <div class="task-details">
                <h3>Task Details:</h3>
                <p><strong>Title:</strong> {{ $task->title }}</p>
                <p><strong>Project:</strong> {{ $task->project->name }}</p>
                <p><strong>Assigned to:</strong> {{ $task->assignedUser ? $task->assignedUser->name : 'Unassigned' }}</p>
                <p><strong>Priority:</strong> {{ ucfirst(str_replace('_', ' ', $task->priority)) }}</p>
                <p><strong>Due Date:</strong> {{ $task->due_date ? $task->due_date->format('F d, Y') : 'Not set' }}</p>
                @if($task->description)
                <p><strong>Description:</strong><br>{{ $task->description }}</p>
                @endif
            </div>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/admin/tasks/' . $task->id) }}" class="button">View Task</a>
            </p>
            
            <p>Best regards,<br>
            <!-- {{ config('app.name') }} Team</p> -->
              <p>Quick Drive Team</p>
        </div>
    </div>
</body>
</html>