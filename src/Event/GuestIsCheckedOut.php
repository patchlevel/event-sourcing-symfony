<?php

namespace App\Event;

use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('hotel.guest_is_checked_out')]
final class GuestIsCheckedOut
{
    public function __construct(
        public readonly Uuid $hotelId,
        public readonly string $guestName,
    ) {
    }
}