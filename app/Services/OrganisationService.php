<?php

declare(strict_types=1);

namespace App\Services;

use App\Organisation;
use App\User;

/**
 * Class OrganisationService
 * @package App\Services
 */
class OrganisationService
{
    /**
     * @param array $attributes
     *
     * @param User $user
     * @return Organisation
     */
    public function createOrganisation(array $attributes, User $user): Organisation
    {
        $organisation = new Organisation();

        $organisation->name = $attributes[ 'name' ];
        $organisation->owner()->associate($user);

        $organisation->save();

        return $organisation;
    }

    /**
     * @param bool|string $filter
     * @return array
     */
    public function listFilterOrganisations($filter)
    {
        $organisations = Organisation::all();

        if ( $filter === 'subbed' ) {
            return $organisations->where('subscribed', 1);
        }

        if ( $filter === 'trial' ) {
            return $organisations->where('subscribed', 0);
        }

        return $organisations;
    }
}
