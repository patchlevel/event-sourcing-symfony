<?php

namespace App\Controller;

use App\Aggregate\Hotel;
use App\Subscriber\GuestProjection;
use Patchlevel\EventSourcing\Aggregate\Uuid;
use Patchlevel\EventSourcing\Repository\Repository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class HotelController
{
    /**
     * @param Repository<Hotel> $hotelRepository
     */
    public function __construct(
        private readonly Repository $hotelRepository,
        private readonly GuestProjection $guestProjection,
    ) {
    }

    #[Route('/{hotelId}/guests', methods:['GET'])]
    public function hotelGuestsAction(Uuid $hotelId): JsonResponse
    {
        return new JsonResponse(
            $this->guestProjection->findGuestsByHotelId($hotelId),
        );
    }

    #[Route('/create', methods:['POST'])]
    public function createAction(Request $request): JsonResponse
    {
        $hotelName = $request->getPayload()->get('name');
        $id = Uuid::generate();

        $hotel = Hotel::create($id, $hotelName);
        $this->hotelRepository->save($hotel);

        return new JsonResponse(['id' => $id->toString()]);
    }

    #[Route('/{hotelId}/check-in', methods:['POST'])]
    public function checkInAction(Uuid $hotelId, Request $request): JsonResponse
    {
        $guestName = $request->getPayload()->get('name'); // need validation!

        $hotel = $this->hotelRepository->load($hotelId);
        $hotel->checkIn($guestName);
        $this->hotelRepository->save($hotel);

        return new JsonResponse();
    }

    #[Route('/{hotelId}/check-out', methods:['POST'])]
    public function checkOutAction(Uuid $hotelId, Request $request): JsonResponse
    {
        $guestName = $request->getPayload()->get('name'); // need validation!

        $hotel = $this->hotelRepository->load($hotelId);
        $hotel->checkOut($guestName);
        $this->hotelRepository->save($hotel);

        return new JsonResponse();
    }
}