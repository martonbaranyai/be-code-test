<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\OrganisationCreated;
use App\Organisation;
use App\Services\OrganisationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class OrganisationController
 * @package App\Http\Controllers
 */
class OrganisationController extends ApiController
{
    /**
     * @param Request $request
     * @param OrganisationService $service
     *
     * @return JsonResponse
     */
    public function store(Request $request, OrganisationService $service): JsonResponse
    {
        $request->validate([
            'name' => 'required'
        ]);

        /** @var Organisation $organisation */
        $organisation = $service->createOrganisation($this->request->all(), $request->user());

        \Mail::to($request->user()->email)
            ->send(new OrganisationCreated($organisation));

        return $this
            ->transformItem('organisation', $organisation, ['user'])
            ->respond();
    }

    /**
     * @param Request $request
     * @param OrganisationService $service
     * @return JsonResponse
     */
    public function listAll(Request $request, OrganisationService $service)
    {
        $filter = $request->get('filter', false);

        $organisations = $service->listFilterOrganisations($filter);

        return $this
            ->transformCollection('organisation', $organisations)
            ->respond();
    }
}
