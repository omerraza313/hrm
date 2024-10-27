<?php

namespace App\Events\Admin;

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

    public $employee;
    public $apply_leave;

    public $json_apply_leave;

    public int|string $leave_count;
    /**
     * Create a new event instance.
     */
    public function __construct(User $employee, string|int $apply_leaves_id)
    {
        $this->employee = $employee;

        $this->apply_leave = Applyleaves::where('id', $apply_leaves_id)->with(['employee', 'employee.employee_details.designation', 'approved_by', 'adjust_leaves', 'adjust_leaves.leave_plan'])->first();

        $this->json_apply_leave = json_encode($this->apply_leave);

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

    public function broadcastAs()
    {
        return 'apply-leave';
    }
}
