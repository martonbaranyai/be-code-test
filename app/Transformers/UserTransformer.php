<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Organisation;
use App\User;
use League\Fractal\TransformerAbstract;

/**
 * Class OrganisationTransformer
 * @package App\Transformers
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user): array
    {
        return ['name' => $user->name, 'email' => $user->email];
    }
}
