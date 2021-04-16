<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeliveryDayModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    public $user;

    /**
     * setting up a user to be used in all tests
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        $this->user = User::factory()->create();
    }

    /**
     * test the index page
     * 
     * @return void
     */
    public function testAllowIndexPage()
    {
        $this->withoutExceptionHandling();
        $this->user->givePermissionTo('model_access');
        
        $response = $this->actingAs($this->user)->get(route('index'));

        $response->assertOk();
        $response->assertViewIs('index');
    }

    /**
     * test the index page when not logged in
     * 
     * @return void
     */
    public function testDeniedIndexPageWhenNotLoggedIn()
    {
        $response = $this->get(route('index'));

        $this->assertGuest($guard = null);
        $response->assertRedirect('/login');
    }

    /**
     * test the index page without permission
     * 
     * @return void
     */
    public function testDeniedIndexPageWithoutPermission()
    {
        $response = $this->actingAs($this->user)->get(route('index'));

        $response->assertForbidden();
    }

    /**
     * test the Create page
     * 
     * @return void
     */
    public function testAllowCreatePage()
    {
        $this->withoutExceptionHandling();
        $this->user->givePermissionTo('model_create');
        
        $response = $this->actingAs($this->user)->get(route('create'));

        $response->assertOk();
        $response->assertViewIs('create');
    }

    /**
     * test the Create page when not logged in
     * 
     * @return void
     */
    public function testDeniedCreatePageWhenNotLoggedIn()
    {
        $response = $this->get(route('create'));

        $this->assertGuest($guard = null);
        $response->assertRedirect('/login');
    }

    /**
     * test the Create page without permission
     * 
     * @return void
     */
    public function testDeniedCreatePageWithoutPermission()
    {
        $response = $this->actingAs($this->user)->get(route('create'));

        $response->assertForbidden();
    }

    /**
     * test a model can be created
     * 
     * @return void
     */
    public function testAModelCanBeCreated()
    {
        $this->withoutExceptionHandling();
        $this->user->givePermissionTo('model_create');
        
        $data = [
            'col1' => $this->faker->word(),
            'col2' => $this->faker->word(),
        ];

        $response = $this->actingAs($this->user)->post(route('store'), $data);

        $model = Model::where('col1', $data['col1'])->first();

        $this->assertDatabaseHas(Model::getTableName(), $data);
        $response->assertSessionHas('state', 'success');
        $response->assertSessionHas('message', 'The Model was created successfully');
        $response->assertRedirect(route('show', [$model->id]));
    }

    /**
     * test a model cannot be created when not logged in
     * 
     * @return void
     */
    public function testDeniedCreatingAModelWhenNotLoggedIn()
    {
        $data = [
            'col1' => $this->faker->word(),
            'col2' => $this->faker->word(),
        ];

        $response = $this->post(route('store'), $data);

        $this->assertGuest($guard = null);
        $response->assertRedirect('/login');
    }

    /**
     * test a model cannot be created without permission
     * 
     * @return void
     */
    public function testDeniedCreatingAModelWithoutPermission()
    {
        $data = [
            'col1' => $this->faker->word(),
            'col2' => $this->faker->word(),
        ];

        $response = $this->actingAs($this->user)->post(route('store'), $data);

        $response->assertForbidden();
    }

    /**
     * test a model cannot be created when required fields are empty
     * 
     * @return void
     */
    public function testErrorCreatingAModelWhenRequiredFieldsAreEmpty()
    {
        $this->user->givePermissionTo('model_create');
        
        $data = [
            'col1' => null,
            'col2' => null,
        ];

        $response = $this->actingAs($this->user)->post(route('store'), $data);

        $response->assertSessionHasErrors(['col1', 'col2']);
    }

    /**
     * test the Show page
     * 
     * @return void
     */
    public function testAllowShowPage()
    {
        $this->withoutExceptionHandling();
        $this->user->givePermissionTo('model_show');
        $model = Model::factory()->create();

        $response = $this->actingAs($this->user)->get(route('show', $model));

        $response->assertOk();
        $response->assertViewIs('show');
    }

    /**
     * test the Show page when not logged in
     * 
     * @return void
     */
    public function testDeniedShowPageWhenNotLoggedIn()
    {
        $model = Model::factory()->create();

        $response = $this->get(route('show', $model));

        $this->assertGuest($guard = null);
        $response->assertRedirect('/login');
    }

    /**
     * test the Show page without permission
     * 
     * @return void
     */
    public function testDeniedShowPageWithoutPermission()
    {
        $model = Model::factory()->create();

        $response = $this->actingAs($this->user)->get(route('show', $model));

        $response->assertForbidden();
    }

    /**
     * test the Edit page
     * 
     * @return void
     */
    public function testAllowEditPage()
    {
        $this->withoutExceptionHandling();
        $this->user->givePermissionTo('model_edit');
        $model = Model::factory()->create();

        $response = $this->actingAs($this->user)->get(route('edit', $model));

        $response->assertOk();
        $response->assertViewIs('edit');
    }

    /**
     * test the Edit page when not logged in
     * 
     * @return void
     */
    public function testDeniedEditPageWhenNotLoggedIn()
    {
        $model = Model::factory()->create();

        $response = $this->get(route('edit', $model));

        $this->assertGuest($guard = null);
        $response->assertRedirect('/login');
    }

    /**
     * test the Edit page without permission
     * 
     * @return void
     */
    public function testDeniedEditPageWithoutPermission()
    {
        $model = Model::factory()->create();

        $response = $this->actingAs($this->user)->get(route('edit', $model));

        $response->assertForbidden();
    }

    /**
     * test a model can be updated
     * 
     * @return void
     */
    public function testAModelCanBeUpdated()
    {
        $this->withoutExceptionHandling();
        $this->user->givePermissionTo('model_edit');
        $model = Model::factory()->create();

        $data = [
            'col1' => $this->faker->word(),
            'col2' => $this->faker->word(),
        ];

        $response = $this->actingAs($this->user)->patch(route('update', $model), $data);

        $this->assertDatabaseHas(Model::getTableName(), $data);
        $response->assertSessionHas('state', 'success');
        $response->assertSessionHas('message', 'The Model was updated successfully');
        $response->assertRedirect(route('show', [$model->id]));
    }

    /**
     * test a model cannot be updated when not logged in
     * 
     * @return void
     */
    public function testDeniedUpdatingAModelWhenNotLoggedIn()
    {
        $model = Model::factory()->create();

        $data = [
            'col1' => $this->faker->word(),
            'col2' => $this->faker->word(),
        ];

        $response = $this->patch(route('update', $model), $data);

        $this->assertGuest($guard = null);
        $response->assertRedirect('/login');
    }

    /**
     * test a model cannot be updated without permission
     * 
     * @return void
     */
    public function testDeniedUpdatingAModelWithoutPermission()
    {
        $model = Model::factory()->create();

        $data = [
            'col1' => $this->faker->word(),
            'col2' => $this->faker->word(),
        ];

        $response = $this->actingAs($this->user)->patch(route('update', $model), $data);

        $response->assertForbidden();
    }

    /**
     * test a model cannot be updated when required fields are empty
     * 
     * @return void
     */
    public function testErrorUpdatingAModelWhenRequiredFieldsAreEmpty()
    {
        $this->user->givePermissionTo('model_edit');
        $model = Model::factory()->create();

        $data = [
            'col1' => null,
            'col2' => null,
        ];

        $response = $this->actingAs($this->user)->patch(route('update', $model), $data);

        $this->assertDatabaseHas(Model::getTableName(), [
            'col1' => $model->col1,
            'col2' => $model->col2,
        ]);
        $response->assertSessionHasErrors(['col1', 'col2']);
    }

    /**
     * test a model can be deleted
     * 
     * @return void
     */
    public function testAModelCanBeDeleted()
    {
        $this->withoutExceptionHandling();
        $this->user->givePermissionTo('model_delete');
        $model = Model::factory()->create();
        
        $response = $this->actingAs($this->user)->delete(route('destroy', $model));

        $this->assertDeleted($model);
        $response->assertRedirect(route('index'));
    }

    /**
     * test a model cannot be deleted when not logged in
     * 
     * @return void
     */
    public function testDeniedDeletingAModelWhenNotLoggedIn()
    {
        $model = Model::factory()->create();
        
        $response = $this->delete(route('destroy', $model));

        $this->assertGuest($guard = null);
        $response->assertRedirect('/login');
    }

    /**
     * test a model cannot be deleted without permission
     * 
     * @return void
     */
    public function testDeniedDeletingAModelWithoutPermission()
    {
        $model = Model::factory()->create();
        
        $response = $this->actingAs($this->user)->delete(route('destroy', $model));

        $response->assertForbidden();
    }
}
