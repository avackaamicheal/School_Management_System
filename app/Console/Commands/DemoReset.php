<?php

namespace App\Console\Commands;

use App\Models\School;
use Database\Seeders\DemoSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DemoReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:demo-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wipe and reseed the demo school. DO NOT run this against production data.';

    /**
     * Execute the console command.
     */

    protected string $demoSlug = 'demo-school-academy';

    public function handle()
    {
        $this->warn('This will DELETE the existing demo school and all its data, then reseed it fresh.');

        if (!$this->confirm('Are you sure you want to continue?', true)) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        $school = School::where('slug', $this->demoSlug)->first();

        if ($school) {
            $this->info("Deleting existing demo school (ID {$school->id})...");
            DB::transaction(function () use ($school) {
                // Cascade-delete relies on foreign key constraints where set.
                // Explicitly clean up users tied to this school as a safety net.
                \App\Models\User::where('school_id', $school->id)->each(function ($user) {
                    $user->delete();
                });

                $school->delete();
            });

            $this->info('Existing demo data removed.');
        } else {
            $this->info('No existing demo school found — seeding fresh.');
        }

        $this->info('Reseeding demo school...');
        Artisan::call('db:seed', ['--class' => DemoSeeder::class, '--force' => true]);
        $this->line(Artisan::output());

        $this->info('Demo reset complete.');

        return self::SUCCESS;
    }
}
