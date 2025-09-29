<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGetDriverOnlineOfflineDurationProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetDriverOnlineOfflineDuration');

        DB::unprepared('
            CREATE PROCEDURE GetDriverOnlineOfflineDuration(
                IN p_driver_id INT,
                IN p_from_date DATE,
                IN p_to_date DATE
            )
            BEGIN
                SELECT 
                    DATE(online_at) AS date,
                    SUM(duration) / 60 AS total_duration_hours
                FROM 
                    driver_availabilities
                WHERE 
                    driver_id = p_driver_id
                    AND DATE(online_at) BETWEEN p_from_date AND p_to_date
                GROUP BY 
                    DATE(online_at);
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetDriverOnlineOfflineDuration');
    }
}
