<?php

// app/Notifications/FollowUpReminder.php
namespace App\Notifications;

use App\Models\Collaboration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpReminder extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(public Collaboration $collab) {}

  public function via($notifiable): array { return ['mail']; }

  public function toMail($notifiable): MailMessage
  {
    $sponsorName = optional($this->collab->sponsor)->name;
    return (new MailMessage)
      ->subject("Follow-up reminder: {$this->collab->title}")
      ->line("Sponsor: " . ($sponsorName ?: '—'))
      ->line("Status: {$this->collab->status}")
      ->line("Follow-up date: " . ($this->collab->follow_up_date ?: '—'))
      ->action('Open collaboration', url("/collaborations?mode=collaborations&q=" . urlencode($this->collab->title)));
  }
}

