<?php

namespace Tests\Feature;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaceAccuracyBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to simulate client-side calibrated biometric confidence scoring.
     */
    private function calculateCalibratedScore(float $distance, float $threshold = 0.55): int
    {
        if ($distance <= $threshold) {
            $score = 100 - ($distance / $threshold) * 20;

            return (int) max(80, min(99, round($score)));
        }

        $excess = ($distance - $threshold) / (1.0 - $threshold);

        return (int) max(0, min(74, round(75 - $excess * 75)));
    }

    /**
     * Helper to calculate Euclidean distance between two 128D vectors.
     */
    private function calculateEuclideanDistance(array $vec1, array $vec2): float
    {
        $sum = 0.0;
        for ($i = 0; $i < count($vec1); $i++) {
            $diff = $vec1[$i] - $vec2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    public function test_calibrated_biometric_confidence_scoring_on_true_matches(): void
    {
        // On authentic employee faces, Euclidean distance typically ranges 0.30 - 0.52
        // All these must produce high, confident scores (80% - 99%)
        $scoreAtZero = $this->calculateCalibratedScore(0.0);
        $scoreAt30 = $this->calculateCalibratedScore(0.30);
        $scoreAt45 = $this->calculateCalibratedScore(0.45);
        $scoreAt50 = $this->calculateCalibratedScore(0.50);
        $scoreAtBoundary = $this->calculateCalibratedScore(0.55);

        $this->assertEquals(99, $scoreAtZero); // Capped max 99% in implementation
        $this->assertGreaterThanOrEqual(88, $scoreAt30);
        $this->assertGreaterThanOrEqual(83, $scoreAt45);
        $this->assertGreaterThanOrEqual(81, $scoreAt50);
        $this->assertEquals(80, $scoreAtBoundary);
    }

    public function test_calibrated_biometric_confidence_scoring_on_mismatches(): void
    {
        // On imposters or different people, distance exceeds 0.55
        // Scores must drop below 75% rapidly
        $scoreAt60 = $this->calculateCalibratedScore(0.60);
        $scoreAt70 = $this->calculateCalibratedScore(0.70);
        $scoreAt85 = $this->calculateCalibratedScore(0.85);
        $scoreAt100 = $this->calculateCalibratedScore(1.0);

        $this->assertLessThanOrEqual(74, $scoreAt60);
        $this->assertLessThan(55, $scoreAt70);
        $this->assertLessThan(30, $scoreAt85);
        $this->assertEquals(0, $scoreAt100);
    }

    public function test_euclidean_distance_calculation_with_synthetic_128d_vectors(): void
    {
        // 128D identical unit vector has distance 0.0
        $vecA = array_fill(0, 128, 1.0 / sqrt(128));
        $this->assertEqualsWithDelta(0.0, $this->calculateEuclideanDistance($vecA, $vecA), 0.0001);

        // Small perturbation simulating normal intra-subject lighting variance (dist ~0.35)
        $vecB = $vecA;
        $vecB[0] += 0.25;
        $vecB[1] -= 0.25;
        $dist = $this->calculateEuclideanDistance($vecA, $vecB);

        $this->assertLessThan(0.55, $dist);
        $this->assertGreaterThan(0.20, $dist);
    }

    public function test_attendance_does_not_overwrite_master_descriptor_on_daily_scan(): void
    {
        $shift = Shift::first() ?? Shift::create([
            'name' => 'Shift Normal',
            'start_time' => '07:00:00',
            'end_time' => '15:00:00',
            'late_tolerance_minutes' => 15,
        ]);

        $initialMasterDescriptor = json_encode(array_fill(0, 128, 0.05));

        $user = User::factory()->create([
            'role' => 'user',
            'account_status' => 'Aktif',
            'shift_id' => $shift->id,
            'registered_face_photo' => 'uploads/avatars/test_master.jpg',
            'face_descriptor' => $initialMasterDescriptor,
        ]);

        $newDailyDescriptor = json_encode(array_fill(0, 128, 0.99));

        $this->actingAs($user);

        // Generate tiny 1x1 base64 gif image
        $dummyPhoto = 'data:image/jpeg;base64,'.base64_encode(str_repeat('A', 100));

        $response = $this->post(route('employee.attendance.store'), [
            'type' => 'MASUK',
            'lat' => -6.2087634,
            'lng' => 106.8455992,
            'photo_data' => $dummyPhoto,
            'face_descriptor' => $newDailyDescriptor,
            'face_confidence_score' => 92.5,
        ]);

        $user->refresh();

        // Master template must REMAIN the original, not overwritten by daily scan!
        $this->assertEquals($initialMasterDescriptor, $user->face_descriptor);
    }

    public function test_first_time_registration_saves_master_descriptor(): void
    {
        $shift = Shift::first() ?? Shift::create([
            'name' => 'Shift Normal',
            'start_time' => '07:00:00',
            'end_time' => '15:00:00',
            'late_tolerance_minutes' => 15,
        ]);

        $user = User::factory()->create([
            'role' => 'user',
            'account_status' => 'Pending Biometrik',
            'shift_id' => $shift->id,
            'registered_face_photo' => null,
            'face_descriptor' => null,
        ]);

        $newMasterDescriptor = json_encode(array_fill(0, 128, 0.07));

        $this->actingAs($user);

        $dummyPhoto = 'data:image/jpeg;base64,'.base64_encode(str_repeat('A', 100));

        $response = $this->post(route('employee.attendance.store'), [
            'type' => 'MASUK',
            'lat' => -6.2087634,
            'lng' => 106.8455992,
            'photo_data' => $dummyPhoto,
            'face_descriptor' => $newMasterDescriptor,
            'face_confidence_score' => 100,
        ]);

        $user->refresh();

        // Must now be enrolled and active
        $this->assertEquals('Aktif', $user->account_status);
        $this->assertEquals($newMasterDescriptor, $user->face_descriptor);
        $this->assertNotNull($user->registered_face_photo);
    }
}
