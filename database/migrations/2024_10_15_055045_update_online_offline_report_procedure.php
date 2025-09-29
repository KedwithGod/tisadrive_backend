<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop the existing procedure if it exists
        DB::unprepared('
            DROP PROCEDURE IF EXISTS GetDriverOnlineOfflineDuration;
        ');
    
        // Create the updated procedure
        DB::unprepared('
            CREATE PROCEDURE GetDriverOnlineOfflineDuration(
                IN p_driver_id INT,
                IN p_from_date DATE,
                IN p_to_date DATE
            )
            BEGIN
                SELECT 
                    DATE(da.online_at) AS date,
                    d.name AS driver_name,  -- Fetching the driver name
                    SUM(da.duration) / 60 AS total_duration_hours
                FROM 
                    driver_availabilities da
                JOIN 
                    drivers d ON da.driver_id = d.id  -- Join with drivers table
                WHERE 
                    da.driver_id = p_driver_id
                    AND DATE(da.online_at) BETWEEN p_from_date AND p_to_date
                GROUP BY 
                    DATE(da.online_at), d.name;  -- Group by date and driver name
            END
        ');
    }
    
    public function down()
    {
        // Drop the procedure if needed when rolling back
        DB::unprepared('
            DROP PROCEDURE IF EXISTS GetDriverOnlineOfflineDuration;
        ');
    }
    
};
