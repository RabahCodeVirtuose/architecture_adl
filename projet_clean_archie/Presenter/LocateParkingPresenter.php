<?php

class LocateParkingPresenter
{
    private IParkingRepository $repo;

    public function __construct(IParkingRepository $repo)
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