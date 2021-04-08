<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Messages\SlackAttachment;

class EventReminderNotificationToSlack extends Notification
{
    use Queueable;

    private $event;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content($this->event)
            ->attachment(function (Slackattachment $attachment) {
                $attachment->title('Event Reminder', $this->event)
                    ->fields([
                        'Subject' => 'An event is scheduled for you in less than an hour',
                        'Title' => $this->event->title,
                        'Description' => $this->event->description,
                        'Start_time' => $this->event->start_time  . ' (' . $this->event->timezone . ')',
                    ]);
            });
    }
}
