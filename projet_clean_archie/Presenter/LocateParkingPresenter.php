<?php

class LocateParkingPresenter
{
    private IGetAllParkingRepository $repo;

    public function __construct(IGetAllParkingRepository $repo)
    {
        $this->repo = $repo;
    }

    public function execute(LocateParkingsRequest $request)
    {
        $useCase = new LocateParkingUseCase($this->repo);
		$result = $useCase->execute($request);

		return new LocateParkingViewModel(
			$result->listParkingsInRange,
		);
    }
}
