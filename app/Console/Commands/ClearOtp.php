<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Models\MailOtp;
use App\Models\Admin\PeakZone;
use Illuminate\Console\Command;

class ClearOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'clear:otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '3 hours completed OTP Deleted';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $currentTime = Carbon::now();


        $otps = MailOtp::where('created_at', '<', $currentTime)->get();


        $peak_current_time = $currentTime->timestamp;

        $peak_zones = PeakZone::where('active',true)->where('end_time', '<', $peak_current_time)->get();


      
        foreach ($otps as $otp) 
        {
            $created_time = strtotime($otp->created_at);

            $time =strtotime($currentTime);

            $difference = abs($time - $created_time)/3600;

                if ($difference >= 3)
                 {
                $otp->delete();
                }         
        }
        foreach ($peak_zones as $peakZone) {
            $peakZone->update(['active'=>0]);
            $database->getReference('peak-zones/'.$peakZone->id)->update(['active'=>0]);
        }

       $this->info(' OTP Records cleard ');    }
}