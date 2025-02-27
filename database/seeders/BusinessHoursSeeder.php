<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessHour;

class BusinessHoursSeeder extends Seeder
{
    public function run()
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        foreach ($days as $day) {
            BusinessHour::updateOrCreate(
                ['day' => $day],
                ['open_time' => '08:00:00', 'close_time' => '22:00:00']
            );
        }
    }
}
