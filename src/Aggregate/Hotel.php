<?php
namespace App\Aggregate;

use App\Event\GuestIsCheckedIn;
use App\Event\GuestIsCheckedOut;
use App\Event\HotelCreated;
use Patchlevel\EventSourcing\Aggregate\BasicAggregateRoot;
use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Aggregate;
use Patchlevel\EventSourcing\Attribute\Apply;
use Patchlevel\EventSourcing\Attribute\Id;
use RuntimeException;

use function array_filter;
use function array_values;
use function in_array;
use function sprintf;

#[Aggregate(name: 'hotel')]
final class Hotel extends BasicAggregateRoot
{
    #[Id]
    private Uuid $id;
    private string $name;

    /** @var list<string> */
    private array $guests;

    public static function create(Uuid $id, string $hotelName): self
    {
        $self = new self();
        $self->recordThat(new HotelCreated($id, $hotelName));

        return $self;
    }

    public function checkIn(string $guestName): void
    {
        if (in_array($guestName, $this->guests, true)) {
            throw new RuntimeException(
                sprintf('Guest "%s" is already checked in.', $guestName),
            );
        }

        $this->recordThat(new GuestIsCheckedIn($this->id, $guestName));
    }

    public function checkOut(string $guestName): void
    {
        if (!in_array($guestName, $this->guests, true)) {
            throw new RuntimeException(
                sprintf('Guest "%s" is not checked in.', $guestName),
            );
        }

        $this->recordThat(new GuestIsCheckedOut($this->id, $guestName));
    }

    #[Apply]
    protected function applyHotelCreated(HotelCreated $event): void
    {
        $this->id = $event->hotelId;
        $this->name = $event->hotelName;
        $this->guests = [];
    }

    #[Apply]
    protected function applyGuestIsCheckedIn(GuestIsCheckedIn $event): void
    {
        $this->guests[] = $event->guestName;
    }

    #[Apply]
    protected function applyGuestIsCheckedOut(GuestIsCheckedOut $event): void
    {
        $this->guests = array_values(
            array_filter(
                $this->guests,
                static fn ($name) => $name !== $event->guestName,
            ),
        );
    }
}