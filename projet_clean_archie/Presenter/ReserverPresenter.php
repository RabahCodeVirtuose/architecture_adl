<?php

class ReserverPresenter
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

    public function execute(ReserveSpotRequest $request)
    {
        $useCase = new AddReservationUseCase($this->user_repo, $this->parking_repo, $this->reservation_repo);
        $result = $useCase->execute($request);

        return new AddReservationViewModel(
            $result->price,
            $result->success
        );
    }
}
