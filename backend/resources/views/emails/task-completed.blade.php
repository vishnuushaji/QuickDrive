<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Completed</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #fff; }
        .task-details { background-color: #f9f9f9; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; margin: 5px; }
        .approve { background-color: #28a745; }
        .reject { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Task Completed - Awaiting Approval</h2>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>A task has been marked as completed and requires your review.</p>
            
            <div class="task-details">
                <h3>Task Details:</h3>
                <p><strong>Title:</strong> {{ $task->title }}</p>
                <p><strong>Project:</strong> {{ $task->project->name }}</p>
                <p><strong>Completed by:</strong> {{ $task->assignedUser ? $task->assignedUser->name : 'Unknown' }}</p>
                <p><strong>Completion Date:</strong> {{ now()->format('F d, Y') }}</p>
                @if($task->description)
                <p><strong>Description:</strong><br>{{ $task->description }}</p>
                @endif
            </div>
            
            <p><strong>Action Required:</strong> Please review this task and approve or reject it.</p>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="{{ url('/admin/tasks/' . $task->id) }}" class="button">Review Task</a>
            </p>
            
            <p>Best regards,<br>
            <!-- {{ config('app.name') }} Team</p> -->
              <p>Quick Drive Team</p>
        </div>
    </div>
</body>
</html>