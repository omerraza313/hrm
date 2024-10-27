<?php

namespace App\Events\Employee;

use App\Models\Applyleaves;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplyLeaveEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $employee;

    public Applyleaves $applyLeave;

    public User $admin;


    /**
     * Create a new event instance.
     */
    public function __construct($employee, $applyLeave, $admin)
    {
        $this->employee = $employee;
        $this->applyLeave = $applyLeave;
        $this->admin = $admin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('employee-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'apply-leave';
    }
}
