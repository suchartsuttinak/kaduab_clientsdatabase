<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientPolicy
{
    public function view(User $user, Client $client): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->canAccessProject($client->project_id)
            && $user->canAccessHouse($client->house_id);
    }

    public function update(User $user, Client $client)
    {
        return $this->view($user, $client);
    }

    public function delete(User $user, Client $client)
    {
        return $this->view($user, $client);
    }
}