<?php

namespace App\Event;

use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('hotel.guest_is_checked_in')]
final class GuestIsCheckedIn
{
    public function __construct(
        public readonly Uuid $hotelId,
        public readonly string $guestName,
    ) {
    }
}