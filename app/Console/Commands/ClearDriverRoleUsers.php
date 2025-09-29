<?php

namespace App\Console\Commands;

use App\Models\Admin\Driver;
use Kreait\Firebase\Contract\Database;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Jobs\Notifications\AndroidPushNotification;
use App\Models\User;

class ClearDriverRoleUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:drivers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Database $database)
    {
        parent::__construct();
        $this->database = $database;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $deletable_drivers = User::belongsToRole('driver')->whereDoesntHave('driver')->get();

        foreach ($deletable_drivers as $key => $driver) {
            
            
            $driver->forceDelete();

        }

            $this->info('success');
        
        

    }
}
