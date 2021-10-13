<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NoteTest extends TestCase
{
    /** @test */
    public function unlogged_user_cannot_access()
    {
        // $this->withoutExceptionHandling();

        $this->postJson('/api/v1/notes')
                ->assertStatus(401);
    }


    /** @test */
    public function unlogged_user_cannot_list_notes()
    {
        $this->getJson('/api/v1/notes')
                ->assertStatus(401);
    }


    /** @test */
    public function add_validation_to_note_creation()
    {
        $this->userLogin();

        $this->postJson('/api/v1/notes', [])
                // ->dump()
                ->assertJsonValidationErrors(['content']);
    }

    /** @test */
    public function store_note_successfully()
    {
        $this->withoutExceptionHandling();
        $this->userLogin();

        $attributes = ['content' => $this->faker->sentence()];

        $this->postJson('/api/v1/notes', $attributes)
            // ->dump()
            ->assertStatus(201)
            ->assertJsonPath('data.user_name', auth()->user()->name)
            ;

        // Assert note in DB
        $this->assertDatabaseHas('notes', ['user_id' => auth()->id()] + $attributes);
    }


    /** @test */
    public function index_of_notes()
    {
        // $this->withoutExceptionHandling();
        $this->userLogin();

        Note::factory()->create();

        $this->getJson('/api/v1/notes')
                // ->dump()
                ->assertOk()
                ->assertJsonPath('data.0.user_name', auth()->user()->name)
                ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function show_note()
    {
        // $this->withoutExceptionHandling();
        $this->userLogin();

        $note = Note::factory()->create();

        $this->getJson('/api/v1/notes/' . $note->id)
                // ->dump()
                ->assertOk()
                ->assertSee('user_name') // => assert using resource
                ->assertJsonPath('data.id', $note->id);
    }
    /** @test */
    public function update_a_note()
    {
        // $this->withoutExceptionHandling();
        $this->userLogin();

        $note = Note::factory()->create();

        $data = [
            'content' => $this->faker->sentence(),
        ];

        $this->putJson('/api/v1/notes/' . $note->id, $data)
                ->assertOk()
                ->assertJsonPath('data.content', $data['content'])
                ->assertJsonPath('data.user_name', auth()->user()->name);

        $this->assertDatabaseHas('notes', ['content' => $data['content']]);
    }

    /** @test */
    public function delete_note()
    {
        // $this->withoutExceptionHandling();
        $this->userLogin();

        $note = Note::factory()->create();

        $this->deleteJson('/api/v1/notes/' . $note->id)
                // ->dump()
                ->assertOk();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    /** @test */
    public function user_manage_only_his_notes()
    {
        // $this->withoutExceptionHandling();

        // Create users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create notes
        $note1 = Note::factory()->create(['user_id' => $user1->id]);
        $note2 = Note::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);

        // Assert index
        $this->getJson('/api/v1/notes')
                // ->dump()
                ->assertOk()
                ->assertJsonMissing(['id' => $note2->id]);

        // Assert show
        $this->getJson('/api/v1/notes/' . $note2->id)
                ->assertForbidden()
                ->assertJsonMissing(['id' => $note2->id]);


        $data = [
            'content' => $this->faker->sentence(),
        ];

        // Assert update
        $this->putJson('/api/v1/notes/' . $note2->id, $data)
                ->assertForbidden();

        // Assert delete
        $this->deleteJson('/api/v1/notes/' . $note2->id, $data)
                ->assertForbidden();
    }


    /** @test */
    public function mine_works_properly()
    {
        // Create users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create notes
        $note1 = Note::factory()->create(['user_id' => $user1->id]);
        $note2 = Note::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);

        $this->assertTrue($note1->isMine);
        $this->assertFalse($note2->isMine);
    }
}
