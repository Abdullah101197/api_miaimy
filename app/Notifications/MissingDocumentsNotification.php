<?php
// app/Notifications/MissingDocumentsNotification.php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class MissingDocumentsNotification extends Notification
{
    private $applicantName;
    private $applicantID;
    private $missingDocuments;

    public function __construct($applicantName, $applicantID, $missingDocuments)
    {
        $this->applicantName = $applicantName;
        $this->missingDocuments = $missingDocuments;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->missingDocuments == "empty") {
            $message = new MailMessage();
            $message->subject('Application submitted successfully Alert')
                ->greeting('Hello, ' . $this->applicantName)
                ->line('We have received your admission application has been submitted successfully.')
                ->line('Thank you for your attention.')
                ->salutation('Best regards');
            return $message;
        } else {
            $message = new MailMessage();
            $message->subject('Missing Documents Alert')
                ->greeting('Hello, ' . $this->applicantName)
                ->line('We have received your admission application, but some documents are missing.')
                ->line('The following documents are still required: ' . implode(', ', $this->missingDocuments))
                ->line('Please submit the missing documents as soon as possible to proceed with your application.')
                ->line('Thank you for your attention.')
                ->salutation('Best regards');
            return $message;
        }
    }
}
