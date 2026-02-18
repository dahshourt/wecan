<?php

namespace App\Console\Commands;

use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Models\CabCrUser;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutoApproveCabUsers extends Command
{
    protected $signature = 'cab:approve-users';

    protected $description = 'Automatically approve cab_cr_users after 2 days if not approved';

    public function handle()
    {
        $thresholdDate = $this->getThresholdDate(2);

        // Get users who are not approved and older than 2 days
        $users = CabCrUser::where('status', '0')
            ->whereHas('cabCr', function ($query) {
                $query->where('status', '0');
            })
            ->with('cabCr')
            ->where('created_at', '<=', $thresholdDate)
            ->get();

        $repo = new ChangeRequestRepository();
        $approvedCount = 0;

        foreach ($users as $user) {
            $crId = $user->cabCr->cr_id ?? null;

            if (! $crId) {
                Log::warning("CabCrUser ID {$user->id} has no associated CR ID.");

                continue;
            }

            $requestData = new Request([
                'old_status_id' => '38',
                'new_status_id' => '160',
                'cab_cr_flag' => '1',
                'user_id' => $user->user_id,
                'cron_status_log_message' => "Change Request Approved by System due to CAB member no response. That is considered as a passive approval and the status changed to ':status_name'",
            ]);

            try {
                Log::info('Auto-approved user for CR ID: ' . $crId);
                $repo->update($crId, $requestData);
                $approvedCount++;
            } catch (Exception $e) {
                Log::error("Failed to update CR ID {$crId}: " . $e->getMessage());
            }
        }

        $this->info("Auto-approved $approvedCount user(s).");
    }

    private function getThresholdDate(int $daysToSubtract): Carbon
    {
        $thresholdDate = Carbon::now();

        while ($daysToSubtract > 0) {
            $thresholdDate->subDay();

            // Skip Friday (5) and Saturday (6) according to ISO-8601 (Mon=1, Sun=7)
            if (! in_array($thresholdDate->dayOfWeekIso, [5, 6])) {
                $daysToSubtract--;
            }
        }

        return $thresholdDate;
    }
}
