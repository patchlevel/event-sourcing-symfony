<?php

namespace App\Subscriber;

use App\Event\GuestIsCheckedIn;
use Patchlevel\EventSourcing\Attribute\Processor;
use Patchlevel\EventSourcing\Attribute\Subscribe;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

use function sprintf;

#[Processor('admin_emails')]
final class SendCheckInEmailProcessor
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    #[Subscribe(GuestIsCheckedIn::class)]
    public function onGuestIsCheckedIn(GuestIsCheckedIn $event): void
    {
        $email = (new Email())
            ->from('noreply@patchlevel.de')
            ->to('hq@patchlevel.de')
            ->subject('Guest is checked in')
            ->text(sprintf('A new guest named "%s" is checked in', $event->guestName));

        $this->mailer->send($email);
    }
}