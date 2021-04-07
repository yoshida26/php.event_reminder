<?php

namespace App\Console\Commands;

use App\Event;
use App\Notifications\EventReminderNotificationToSlack;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendRemindersCommandToSlack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:slack';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends reminders to Slack';

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
        $count = 0;
        $times = [
            now('Europe/London')->subDay(),
            now('Europe/London')->addDay()
        ];

        $events = Event::with('registrants')
            ->whereHas('registrants')
            ->whereBetween('start_time', $times)
            ->get();

        foreach ($events as $event) {
            if (
                now($event->timezone)->diffInMinutes($event->start_time, false) <= 60
            ) {
                Notification::send($event->registrants, new EventReminderNotificationToSlack($event));
                $count++;
            }
        }

        $this->info("Event reminders of " . $count . " events has been successfully!");
    }
}
