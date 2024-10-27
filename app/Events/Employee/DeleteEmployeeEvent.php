<?php

namespace App\Events\Employee;

use App\Models\User;
use App\Models\Applyleaves;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DeleteEmployeeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $employee;

    public int|string $apply_leave_id;

    public int|string $leave_count;
    /**
     * Create a new event instance.
     */
    public function __construct($employee_id, $apply_leave_id)
    {
        $this->employee = User::find($employee_id);
        $this->apply_leave_id = $apply_leave_id;
        $this->leave_count = Applyleaves::where('status', 0)->count();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-channel'),
        ];
    }

    public function broadcastAs(){
        return 'delete-employee-leave';
    }
}
