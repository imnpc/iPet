<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PetOwnershipAccessTest extends TestCase
{
    public function test_guest_can_open_pet_profile_from_post_pet_link(): void
    {
        $pet = Pet::factory()->create();

        $response = $this->get(route('pets.show', $pet));

        $response->assertOk();
        $response->assertSee($pet->name);
    }

    public function test_user_cannot_edit_or_update_or_delete_other_users_pet(): void
    {
        $ownerModel = User::factory()->createOne();
        $otherUserModel = User::factory()->createOne();
        $owner = User::query()->findOrFail($ownerModel->id);
        $otherUser = User::query()->findOrFail($otherUserModel->id);
        $pet = Pet::factory()->for($owner)->createOne();

        /** @var User $otherUser */
        $this->actingAs($otherUser)->get(route('pets.edit', $pet))->assertForbidden();
        $this->actingAs($otherUser)->put(route('pets.update', $pet), [
            'name' => '越权修改',
            'species' => '猫',
        ])->assertForbidden();
        $this->actingAs($otherUser)->delete(route('pets.destroy', $pet))->assertForbidden();
    }

    public function test_owner_can_edit_and_update_own_pet(): void
    {
        $ownerModel = User::factory()->createOne();
        $owner = User::query()->findOrFail($ownerModel->id);
        $pet = Pet::factory()->for($owner)->createOne([
            'name' => '旧名字',
            'species' => '狗',
        ]);

        /** @var User $owner */
        $this->actingAs($owner)->get(route('pets.edit', $pet))->assertOk();

        /** @var User $owner */
        $response = $this->actingAs($owner)->put(route('pets.update', $pet), [
            'name' => '新名字',
            'species' => '猫',
            'breed' => '英短',
            'gender' => 'female',
        ]);

        $response->assertRedirect(route('pets.show', $pet));

        $pet->refresh();
        $this->assertSame('新名字', $pet->name);
        $this->assertSame('猫', $pet->species);
    }

    public function test_owner_can_upload_pet_avatar_when_updating_pet(): void
    {
        Storage::fake('public');

        $ownerModel = User::factory()->createOne();
        $owner = User::query()->findOrFail($ownerModel->id);
        $pet = Pet::factory()->for($owner)->createOne([
            'name' => '旧头像宠物',
            'species' => '狗',
        ]);

        $response = $this->actingAs($owner)->put(route('pets.update', $pet), [
            'name' => '新头像宠物',
            'species' => '狗',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect(route('pets.show', $pet));

        $pet->refresh();
        $this->assertStringContainsString('/storage/pets/avatars/', $pet->avatar);
    }

    public function test_guest_can_paginate_pet_posts_on_pet_show_page(): void
    {
        $owner = User::factory()->createOne();
        $pet = Pet::factory()->for($owner)->createOne();

        foreach (range(1, 7) as $number) {
            Post::factory()->for($owner)->for($pet)->createOne([
                'content' => '宠物分页测试动态-'.$number,
                'visibility' => 'public',
                'published_at' => now()->subMinutes(8 - $number),
            ]);
        }

        $pageOneResponse = $this->get(route('pets.show', $pet));
        $pageOneResponse->assertOk();
        $pageOneResponse->assertSee('宠物分页测试动态-7');
        $pageOneResponse->assertSee('宠物分页测试动态-3');
        $pageOneResponse->assertDontSee('宠物分页测试动态-2');
        $pageOneResponse->assertSee('?page=2', false);

        $pageTwoResponse = $this->get(route('pets.show', ['pet' => $pet, 'page' => 2]));
        $pageTwoResponse->assertOk();
        $pageTwoResponse->assertSee('宠物分页测试动态-2');
        $pageTwoResponse->assertSee('宠物分页测试动态-1');
        $pageTwoResponse->assertDontSee('宠物分页测试动态-7');
    }
}
