<?php

namespace Tests\Feature;

use Illuminate\Http\Testing\File;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Activity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_activity_for_any_company()
    {
        $admin = User::factory()->admin()->create();
        $company = Company::factory()->create();
        $response = $this->actingAs($admin)->get(route('companies.activities.create', $company));
        $response->assertOk();
    }

    public function test_company_owner_can_create_activity_for_his_company()
    {
        $company = Company::factory()->create();
        $companyOwner = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $response = $this->actingAs($companyOwner)->get(route('companies.activities.create', $company));
        $response->assertOk();
    }

    public function test_company_owner_cannot_create_activity_for_other_companies()
    {
        $companyOne = Company::factory()->create();
        $companyTwo = Company::factory()->create();
        $companyOwner = User::factory()->companyOwner()->create(['company_id' => $companyOne->id]);
        $response = $this->actingAs($companyOwner)->get(route('companies.activities.create', $companyTwo));
        $response->assertForbidden();
    }

    public function test_guide_or_customer_or_guest_cannot_create_activity_for_any_company()
    {
        $guide = User::factory()->guide()->create();
        $customer = User::factory()->customer()->create();
        $company = Company::factory()->create();

        $responseOne = $this->actingAs($guide)->get(route('companies.activities.create', $company));
        $responseOne->assertForbidden();

        $responseTwo = $this->actingAs($customer)->get(route('companies.activities.create', $company));
        $responseTwo->assertForbidden();

        $this->get(route('companies.activities.create', $company))->assertForbidden();
    }

    //----------------------------------------------------------------------------------------------------------------//

    public function test_admin_can_list_all_activities()
    {
        $admin = User::factory()->admin()->create();
        $company = Company::factory()->create();
        $response = $this->actingAs($admin)->get(route('companies.activities.index', $company));
        $response->assertOk();
    }

    public function test_company_owner_can_list_own_company_activities()
    {
        $company = Company::factory()->create();
        $companyOwner = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $response = $this->actingAs($companyOwner)->get(route('companies.activities.index', $company));
        $response->assertOk();
    }

    public function test_company_owner_cannot_list_except_own_company_activities()
    {
        $companyOne = Company::factory()->create();
        $companyTwo = Company::factory()->create();
        $companyOwner = User::factory()->companyOwner()->create(['company_id' => $companyOne->id]);
        $response = $this->actingAs($companyOwner)->get(route('companies.activities.index', $companyTwo));
        $response->assertForbidden();
    }

    public function test_guide_or_customer_or_guest_cannot_list_activities()
    {
        $guide = User::factory()->guide()->create();
        $customer = User::factory()->customer()->create();
        $company = Company::factory()->create();

        $responseOne = $this->actingAs($guide)->get(route('companies.activities.index', $company));
        $responseOne->assertForbidden();

        $responseTwo = $this->actingAs($customer)->get(route('companies.activities.index', $company));
        $responseTwo->assertForbidden();

        $this->get(route('companies.activities.index', $company))->assertForbidden();
    }

    //----------------------------------------------------------------------------------------------------------------//

    public function test_admin_can_edit_activity_for_any_company()
    {
        $admin = User::factory()->admin()->create();
        $company = Company::factory()->create();
        $activity = Activity::factory()->create();
        $response = $this->actingAs($admin)->get(route('companies.activities.edit', [$company, $activity]));
        $response->assertOk();
    }

    public function test_company_owner_can_edit_activity_for_his_company()
    {
        $company = Company::factory()->create();
        $companyOwner = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $activity = Activity::factory()->create(['company_id' => $company->id]);
        $response = $this->actingAs($companyOwner)->get(route('companies.activities.edit', [$company, $activity]));
        $response->assertOk();
    }

    public function test_company_owner_cannot_edit_activity_for_other_companies()
    {
        $companyOne = Company::factory()->create();
        $companyTwo = Company::factory()->create();
        $companyOwner = User::factory()->companyOwner()->create(['company_id' => $companyOne->id]);
        $activity = Activity::factory()->create(['company_id' => $companyTwo->id]);
        $response = $this->actingAs($companyOwner)->get(route('companies.activities.edit', [$companyTwo, $activity]));
        $response->assertForbidden();
    }

    public function test_guide_or_customer_or_guest_cannot_edit_activity_for_any_company()
    {
        $guide = User::factory()->guide()->create();
        $customer = User::factory()->customer()->create();
        $company = Company::factory()->create();
        $activity = Activity::factory()->create(['company_id' => $company->id]);


        $responseOne = $this->actingAs($guide)->get(route('companies.activities.edit', [$company, $activity]));
        $responseOne->assertForbidden();

        $responseTwo = $this->actingAs($customer)->get(route('companies.activities.edit', [$company, $activity]));
        $responseTwo->assertForbidden();

        $this->get(route('companies.activities.edit', [$company, $activity]))->assertForbidden();
    }

    //----------------------------------------------------------------------------------------------------------------//

    public function test_admin_can_update_activity_for_any_company()
    {
        $admin = User::factory()->admin()->create();
        $company = Company::factory()->create();
        $activity = Activity::factory()->create();
        $response = $this->actingAs($admin)->put(route('companies.activities.update', [$company, $activity]));
        $response->assertRedirect();
    }

    public function test_company_owner_can_update_activity_for_his_company()
    {
        $company = Company::factory()->create();
        $companyOwner = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $activity = Activity::factory()->create(['company_id' => $company->id]);
        $guide = User::factory()->guide()->create();

        $response = $this->actingAs($companyOwner)->put(route('companies.activities.update', [$company, $activity]), [
            'company_id' => $company,
            'guide_id' => $guide->id,
            'name' => 'Test',
            'description' => 'Description of this',
            'start_time' => Carbon::now()->format('Y-m-d H:i:s'),
            'price' => rand(1, 90000),
        ]);

        $response->assertRedirect(route('companies.activities.index', $company));
    }

    public function test_company_owner_cannot_update_activity_for_other_companies()
    {
        $company = Company::factory()->create();
        $company2 = Company::factory()->create();
        $user = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $guide = User::factory()->guide()->create();
        $activity = Activity::factory()->create(['company_id' => $company2->id]);

        $response = $this->actingAs($user)->put(route('companies.activities.update', [$company2, $activity]), [
            'name' => 'activity',
            'description' => 'description',
            'start_time' => '2023-09-01 10:00',
            'price' => 9999,
            'guide_id' => $guide->id,
        ]);

        $response->assertForbidden();
    }

    public function test_guide_or_customer_or_guest_cannot_update_activity_for_any_company()
    {
        $guide = User::factory()->guide()->create();
        $customer = User::factory()->customer()->create();
        $company = Company::factory()->create();
        $activity = Activity::factory()->create(['company_id' => $company->id]);


        $this->actingAs($guide)->put(route('companies.activities.update', [$company, $activity]), [
            'name' => 'activity',
            'description' => 'description',
            'start_time' => '2023-09-01 10:00',
            'price' => 9999,
            'guide_id' => $guide->id,
        ])->assertForbidden();

        $this->actingAs($customer)->put(route('companies.activities.update', [$company, $activity]), [
            'name' => 'activity',
            'description' => 'description',
            'start_time' => '2023-09-01 10:00',
            'price' => 9999,
            'guide_id' => $guide->id,
        ])->assertForbidden();

        $this->put(route('companies.activities.update', [$company, $activity]), [
            'name' => 'activity',
            'description' => 'description',
            'start_time' => '2023-09-01 10:00',
            'price' => 9999,
            'guide_id' => $guide->id,
        ])->assertForbidden();
    }

    //----------------------------------------------------------------------------------------------------------------//

    public function test_admin_can_delete_activity()
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->create();
        $activity = Activity::factory()->create();

        $response = $this->actingAs($admin)->delete(route('companies.activities.destroy', [$company, $activity]));

        $response->assertRedirect(route('companies.activities.index', $company));

        $this->assertModelMissing($activity);
    }

    public function test_company_owner_can_delete_activity()
    {
        $company = Company::factory()->create();
        $user = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $activity = Activity::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)->delete(route('companies.activities.destroy', [$company, $activity]));

        $response->assertRedirect(route('companies.activities.index', $company));

        $this->assertModelMissing($activity);
    }

    public function test_company_owner_cannot_delete_activity_for_other_company()
    {
        $company = Company::factory()->create();
        $company2 = Company::factory()->create();
        $user = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $activity = Activity::factory()->create(['company_id' => $company2->id]);

        $response = $this->actingAs($user)->delete(route('companies.activities.destroy', [$company2, $activity]));

        $this->assertModelExists($activity);
        $response->assertForbidden();
    }

    public function test_guide_or_customer_or_guest_cannot_delete_activity_for_any_company()
    {
        $guide = User::factory()->guide()->create();
        $customer = User::factory()->customer()->create();
        $company = Company::factory()->create();
        $activity = Activity::factory()->create(['company_id' => $company->id]);


        $this->actingAs($guide)->delete(route('companies.activities.destroy', [$company, $activity]))
            ->assertForbidden();

        $this->actingAs($customer)->delete(route('companies.activities.destroy', [$company, $activity]))
            ->assertForbidden();

        $this->delete(route('companies.activities.destroy', [$company, $activity]))
            ->assertForbidden();

        $this->assertModelExists($activity);
    }

    //----------------------------------------------------------------------------------------------------------------//

    public function test_it_can_upload_an_image_for_an_activity()
    {
        Storage::fake('public');

        $image = File::image('photo.jpg');

        $company = Company::factory()->create();
        $user = User::factory()->companyOwner()->create([
            'company_id' => $company->id,
        ]);

        $guide = User::factory()->guide()->create([
            'company_id' => $company->id,
        ]);


        $activityData = [
            'guide_id' =>$guide->id,
            'name' => 'Sample Activity',
            'description' => 'This is a sample description',
            'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'price' => 100.00,
            'image' => $image,
        ];

        $response = $this->actingAs($user)->post(route('companies.activities.store', $company), $activityData);

        $activity = Activity::first();
        $mediaItems = $activity->getMedia('images');

        $this->assertCount(1, $mediaItems);
        $this->assertFileExists($mediaItems->first()->getPath());
        $this->assertFileExists($mediaItems->first()->getPath('thumb'));
        $this->assertFileExists($mediaItems->first()->getPath('small'));

        $response->assertRedirect(route('companies.activities.index', $company));
    }

    public function test_cannon_upload_non_image_file()
    {
        Storage::fake('public');

        $pdf = UploadedFile::fake()->create('Ok.pdf', 1000, 'application/pdf');

        $company = Company::factory()->create();
        $user = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $guide = User::factory()->guide()->create();

        $response = $this->actingAs($user)->post(route('companies.activities.store', $company), [
            'guide_id' => $guide->id,
            'name' => 'Sample Activity',
            'description' => 'This is a sample description',
            'start_time' => now()->addDays(1)->format('Y-m-d H:i:s'),
            'price' => 100.00,
            'image' => $pdf,
        ]);

        $response->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('activities', [
            'name' => 'Sample Activity',
        ]);
    }

    public function test_guides_are_shown_only_for_specific_company_in_create_form()
    {
        $company = Company::factory()->create();
        $user = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $guide = User::factory()->guide()->create(['company_id' => $company->id]);

        $company2 = Company::factory()->create();
        $guide2 = User::factory()->guide()->create(['company_id' => $company2->id]);

        $response = $this->actingAs($user)->get(route('companies.activities.create', $company));

        $response->assertViewHas('guides', function (Collection $guides) use ($guide) {
            return $guide->name === $guides[$guide->id];
        });

        $response->assertViewHas('guides', function (Collection $guides) use ($guide2) {
            return ! array_key_exists($guide2->id, $guides->toArray());
        });
    }

    public function test_guides_are_shown_only_for_specific_company_in_edit_form()
    {
        $company = Company::factory()->create();
        $user = User::factory()->companyOwner()->create(['company_id' => $company->id]);
        $guide = User::factory()->guide()->create(['company_id' => $company->id]);
        $activity = Activity::factory()->create(['company_id' => $company->id]);

        $company2 = Company::factory()->create();
        $guide2 = User::factory()->guide()->create(['company_id' => $company2->id]);

        $response = $this->actingAs($user)->get(route('companies.activities.edit', [$company, $activity]));

        $response->assertViewHas('guides', function (Collection $guides) use ($guide) {
            return $guide->name === $guides[$guide->id];
        });

        $response->assertViewHas('guides', function (Collection $guides) use ($guide2) {
            return ! array_key_exists($guide2->id, $guides->toArray());
        });
    }
}
