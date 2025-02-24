<?php

namespace App\Event;

use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Event;

#[Event('hotel.created')]
final class HotelCreated
{
    public function __construct(
        public readonly Uuid $hotelId,
        public readonly string $hotelName,
    ) {
    }
}