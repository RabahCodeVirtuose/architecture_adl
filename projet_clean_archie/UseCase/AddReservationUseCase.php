<?php

class AddReservationUseCase
{
    private IGetCustomerByEmailRepository $user_repo;
    private IGetParkingByIdRepository $parking_repo;
    private IReservationRepository $reservation_repo;

    public function __construct(
        IGetCustomerByEmailRepository $user_repo, 
        IGetParkingByIdRepository $parking_repo,
        IReservationRepository $reservation_repo
    ) {
        $this->user_repo = $user_repo;
        $this->parking_repo = $parking_repo;
        $this->reservation_repo = $reservation_repo;
    }

    public function execute(ReserveSpotRequest $request): ReserveSpotResponse
    {
        if ($request->start >= $request->end) {
            return new ReserveSpotResponse(ReserveSpotResponse::INVALID_DATES);
        }

        $parking = $this->parking_repo->getParkingById($request->parkingId);
        if ($parking === null) {
            return new ReserveSpotResponse(ReserveSpotResponse::PARKING_NOT_FOUND);
        }

        $user = $this->user_repo->getCustomerByEmail($request->clientEmail);
        if ($user === null) {
            return new ReserveSpotResponse(ReserveSpotResponse::CUSTOMER_NOT_FOUND);
        }

        $overlappingReservations = $this->reservation_repo->findOverlappingByParkingId(
            $parking->getId(),
            $request->start,
            $request->end
        );
        $maximumSimultaneousReservations = $parking->maximumSimultaneousReservations(
            $request->start,
            $request->end,
            $overlappingReservations
        );

        if (!$parking->canAccept($maximumSimultaneousReservations)) {
            return new ReserveSpotResponse(ReserveSpotResponse::CAPACITY_UNAVAILABLE);
        }

        $price = $parking->calculatePrice($request->start, $request->end);
        $reservationId = uniqid('res_');
        $reservation = new Reservation(
            $reservationId,
            $user->getEmail(),
            $parking->getId(),
            $request->start,
            $request->end
        );

        $this->reservation_repo->save($reservation);

        return new ReserveSpotResponse(ReserveSpotResponse::SUCCESS, $price);
    }
}
