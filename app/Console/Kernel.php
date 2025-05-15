<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Ghi log để kiểm tra scheduler
        $schedule->call(function () {
            Log::info('✅ Task schedule chạy OK lúc: ' . now());
        })->everyFiveMinutes();

        // Các lệnh tùy chỉnh
        $schedule->command('posts:auto-post')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('prompts:process')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('analytics:sync')
            ->dailyAt('02:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
