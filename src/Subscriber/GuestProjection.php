<?php

namespace App\Subscriber;

use App\Event\GuestIsCheckedIn;
use App\Event\GuestIsCheckedOut;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Attribute\Projector;
use Patchlevel\EventSourcing\Attribute\Setup;
use Patchlevel\EventSourcing\Attribute\Subscribe;
use Patchlevel\EventSourcing\Attribute\Teardown;
use Patchlevel\EventSourcing\Subscription\Subscriber\SubscriberUtil;

/**
 * @psalm-type GuestData = array{
 *     guest_name: string,
 *     hotel_id: string,
 *     check_in_date: string,
 *     check_out_date: string|null
 * }
 */
#[Projector('guests')]
final class GuestProjection
{
    use SubscriberUtil;

    public function __construct(
        private Connection $db
    ) {
    }

    /** @return list<GuestData> */
    public function findGuestsByHotelId(Uuid $hotelId): array
    {
        return $this->db->createQueryBuilder()
            ->select('*')
            ->from($this->table())
            ->where('hotel_id = :hotel_id')
            ->setParameter('hotel_id', $hotelId->toString())
            ->fetchAllAssociative();
    }

    #[Subscribe(GuestIsCheckedIn::class)]
    public function handleGuestIsCheckedIn(
        GuestIsCheckedIn $event,
        DateTimeImmutable $recordedOn
    ): void {
        $this->db->insert(
            $this->table(),
            [
                'hotel_id' => $event->hotelId->toString(),
                'guest_name' => $event->guestName,
                'check_in_date' => $recordedOn->format('Y-m-d H:i:s'),
                'check_out_date' => null,
            ]
        );
    }

    #[Subscribe(GuestIsCheckedOut::class)]
    public function handleGuestIsCheckedOut(
        GuestIsCheckedOut $event,
        DateTimeImmutable $recordedOn
    ): void {
        $this->db->update(
            $this->table(),
            [
                'check_out_date' => $recordedOn->format('Y-m-d H:i:s'),
            ],
            [
                'hotel_id' => $event->hotelId->toString(),
                'guest_name' => $event->guestName,
                'check_out_date' => null,
            ]
        );
    }

    #[Setup]
    public function create(): void
    {
        $this->db->executeStatement(
        "CREATE TABLE {$this->table()} (
                hotel_id VARCHAR(36) NOT NULL,
                guest_name VARCHAR(255) NOT NULL,
                check_in_date TIMESTAMP NOT NULL,
                check_out_date TIMESTAMP NULL
                );
            "
        );
    }

    #[Teardown]
    public function drop(): void
    {
        $this->db->executeStatement("DROP TABLE IF EXISTS {$this->table()};");
    }

    private function table(): string
    {
        return 'projection_' . $this->subscriberId();
    }
}