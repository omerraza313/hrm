<?php

namespace App\Events\Admin;

use App\Models\User;
use App\Models\Applyleaves;
use App\Models\AssignLeave;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ApplyLeaveAdminEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $employee;
    public $apply_leave;
    public $admin;

    public $assignLeaves;
    /**
     * Create a new event instance.
     */
    public function __construct(User $employee, string|int $apply_leaves_id)
    {
        $this->employee = $employee;
        $this->apply_leave = Applyleaves::where('id', $apply_leaves_id)->with(['approved_by'])->first();
        $this->admin = Auth::user();
        $assignLeaves = AssignLeave::where('user_id', $employee->id)->with('leave_plan')->get()->toArray();
        $this->assignLeaves = $assignLeaves;
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
        return 'admin-apply-leave';
    }
}
