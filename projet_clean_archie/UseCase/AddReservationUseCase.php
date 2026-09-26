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
        $user = $this->user_repo->getCustomerByEmail($request->clientEmail);
        $parking = $this->parking_repo->getParkingById($request->parkingId);

        $activeReservationsCount = $this->reservation_repo->countActiveReservations(
            $parking->getId(),
            $request->start,
            $request->end
        );

        if (!$parking->canAccept($activeReservationsCount)) {
            return new ReserveSpotResponse(false, 0.0);
        }

        $reservationId = uniqid('res_');
        $reservation = new Reservation(
            $reservationId,
            $user->getEmail(),
            $parking->getId(),
            $request->start,
            $request->end
        );

        $this->reservation_repo->save($reservation);

        $price = $parking->calculatePrice($request->start, $request->end);

        return new ReserveSpotResponse(true, $price);
    }
}
