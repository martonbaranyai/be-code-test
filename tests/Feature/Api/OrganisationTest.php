<?php

namespace Tests\Feature;

use App\Organisation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class OrganisationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function list_all_organisations()
    {
        factory(Organisation::class, 50)->create();

        $response = $this->get('api/organisation');

        $response->assertStatus(200);
        $response->assertJsonCount(50, 'data.organisation');
    }

    /** @test */
    public function list_organisations_on_trial()
    {
        factory(Organisation::class, 10)->create(['subscribed' => 1]);
        factory(Organisation::class, 15)->create(['subscribed' => 0]);

        $response = $this->get('api/organisation?filter=trial');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.organisation');
    }

    /** @test */
    public function list_organisations_with_subscription()
    {
        factory(Organisation::class, 10)->create(['subscribed' => 1]);
        factory(Organisation::class, 15)->create(['subscribed' => 0]);

        $response = $this->get('api/organisation?filter=subbed');

        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data.organisation');
    }

    /** @test */
    public function create_organisation()
    {
        $user = factory(User::class)->create(['name' => 'Test Account', 'email' => 'test@test.com']);
        $headers = $this->loginPassport($user);

        $response = $this->post('api/organisation', [
            'name' => 'My test organisation',
        ], $headers);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'My test organisation']);
        $response->assertJsonFragment(['name' => 'Test Account', 'email' => 'test@test.com']);
    }

    /**
     * Helper function
     *
     * @param User $user
     * @return array
     * @throws \Exception
     */
    private function loginPassport(User $user)
    {
        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', ''
        );

        \DB::table('oauth_personal_access_clients')->insert([
            'client_id'  => $client->id,
            'created_at' => new \DateTime,
            'updated_at' => new \DateTime,
        ]);

        $token = $user->createToken('TestToken', [])->accessToken;
        $headers[ 'Accept' ] = 'application/json';
        $headers[ 'Authorization' ] = 'Bearer ' . $token;

        return $headers;
    }
}
