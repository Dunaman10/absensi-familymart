<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 months', '+1 week');
        $durationDays = fake()->randomElement([1, 1, 1, 2, 2, 3, 5]);
        $endDate = (clone $startDate)->modify('+'.($durationDays - 1).' days');

        return [
            'user_id' => User::where('role', 'user')->inRandomOrder()->first()?->id ?? User::factory(),
            'type' => fake()->randomElement(['IZIN', 'CUTI', 'SAKIT']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'duration_days' => $durationDays,
            'reason' => fake()->sentence(8),
            'attachment_path' => null,
            'status' => 'PENDING',
            'reviewed_by' => null,
            'reviewed_at' => null,
            'reject_reason' => null,
        ];
    }

    /**
     * State: approved leave request.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'APPROVED',
            'reviewed_by' => User::where('role', 'admin')->first()?->id,
            'reviewed_at' => fake()->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
        ]);
    }

    /**
     * State: rejected leave request.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'REJECTED',
            'reviewed_by' => User::where('role', 'admin')->first()?->id,
            'reviewed_at' => fake()->dateTimeBetween($attributes['created_at'] ?? '-1 month', 'now'),
            'reject_reason' => fake()->sentence(6),
        ]);
    }
}
