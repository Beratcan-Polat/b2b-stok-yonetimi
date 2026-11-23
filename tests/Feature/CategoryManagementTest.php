<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'tr']);
    }

    public static function listPermissions(): array
    {
        return ['with list permission' => [true], 'without list permission' => [false]];
    }

    public function test_user_with_only_create_permission_can_create_a_category_and_return_home(): void
    {
        $user = $this->userWithPermissions(['kategori.ekle']);

        $this->actingAs($user)->get(route('kategoriler.create'))
            ->assertSee('adminkit/css/app.css')
            ->assertSee('href="'.route('anasayfa').'" class="btn btn-secondary"', false);

        $this->post(route('kategoriler.store'), ['name' => 'Ofis Ürünleri', 'name_en' => 'Office Supplies'])
            ->assertRedirect(route('anasayfa'))
            ->assertSessionHas('success', 'Kategori başarıyla eklendi.');

        $this->assertDatabaseHas('categories', ['name' => 'Ofis Ürünleri', 'name_en' => 'Office Supplies', 'slug' => 'ofis-urunleri']);
    }

    #[DataProvider('listPermissions')]
    public function test_update_returns_to_an_accessible_page_and_generates_the_slug(bool $canList): void
    {
        $permissions = $canList ? ['kategori.duzenle', 'kategori.listele'] : ['kategori.duzenle'];
        $user = $this->userWithPermissions($permissions);
        $category = $this->category();
        $category->update(['name_en' => 'Electronics']);
        $destination = $canList ? 'kategoriler.index' : 'anasayfa';

        $this->actingAs($user)->get(route('kategoriler.edit', $category))
            ->assertSee('adminkit/css/app.css')
            ->assertSee('value="Elektronik"', false)
            ->assertSee('value="Electronics"', false)
            ->assertSee('href="'.route($destination).'" class="btn btn-secondary"', false);

        $this->put(route('kategoriler.update', $category), ['name' => 'Ofis Ürünleri', 'name_en' => 'Office Supplies', 'slug' => 'elle-yazilan'])
            ->assertRedirect(route($destination))
            ->assertSessionHas('success', 'Kategori başarıyla güncellendi.');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Ofis Ürünleri', 'name_en' => 'Office Supplies', 'slug' => 'ofis-urunleri']);
    }

    #[DataProvider('listPermissions')]
    public function test_delete_returns_to_an_accessible_page(bool $canList): void
    {
        $permissions = $canList ? ['kategori.sil', 'kategori.listele'] : ['kategori.sil'];
        $user = $this->userWithPermissions($permissions);
        $category = $this->category();
        $destination = $canList ? 'kategoriler.index' : 'anasayfa';

        $this->actingAs($user)->delete(route('kategoriler.destroy', $category))
            ->assertRedirect(route($destination))
            ->assertSessionHas('success', 'Kategori ve bağlı ürünler başarıyla silindi.');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_create_validation_shows_the_error_and_preserves_the_entered_name(): void
    {
        $user = $this->userWithPermissions(['kategori.ekle']);
        $name = str_repeat('A', 256);

        $this->actingAs($user)->from(route('kategoriler.create'))->followingRedirects()
            ->post(route('kategoriler.store'), ['name' => $name])
            ->assertSeeText('Kategori en fazla 255 karakter olabilir.')
            ->assertSee('value="'.$name.'"', false)
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('aria-describedby="name-error"', false)
            ->assertSee('id="name-error" class="invalid-feedback"', false);

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_update_validation_preserves_the_entered_name_and_existing_slug(): void
    {
        $user = $this->userWithPermissions(['kategori.duzenle']);
        $this->category();
        $category = $this->category('Ofis', 'ofis');

        $this->actingAs($user)->from(route('kategoriler.edit', $category))->followingRedirects()
            ->put(route('kategoriler.update', $category), ['name' => 'Elektronik'])
            ->assertSeeText('Bu kategori adı daha önce kullanıldı.')
            ->assertSee('value="Elektronik"', false)
            ->assertSee('value="ofis" readonly', false)
            ->assertSee('id="name-error" class="invalid-feedback"', false);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Ofis', 'slug' => 'ofis']);
    }

    public function test_category_ajax_actions_include_a_complete_protected_delete_form(): void
    {
        $user = $this->userWithPermissions(['kategori.listele', 'kategori.duzenle', 'kategori.sil']);
        $category = $this->category();

        $response = $this->actingAs($user)->getJson(route('kategoriler.veri'))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Elektronik');

        $actions = $response->json('data.0.actions');
        $this->assertStringContainsString(route('kategoriler.edit', $category), $actions);
        $this->assertStringContainsString(route('kategoriler.destroy', $category), $actions);
        $this->assertStringContainsString('name="_token"', $actions);
        $this->assertStringContainsString('name="_method" value="DELETE"', $actions);
        $this->assertStringContainsString('</form>', $actions);
    }

    public function test_category_operations_are_forbidden_without_the_required_permissions(): void
    {
        $user = User::factory()->create();
        $category = $this->category();

        $this->actingAs($user)->get(route('kategoriler.create'))->assertForbidden();
        $this->get(route('kategoriler.edit', $category))->assertForbidden();
        $this->post(route('kategoriler.store'), ['name' => 'Ofis'])->assertForbidden();
        $this->put(route('kategoriler.update', $category), ['name' => 'Ofis'])->assertForbidden();
        $this->delete(route('kategoriler.destroy', $category))->assertForbidden();

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Elektronik', 'slug' => 'elektronik']);
    }

    #[DataProvider('invalidEnglishNames')]
    public function test_invalid_english_category_names_are_rejected(mixed $name, string $message): void
    {
        $user = $this->userWithPermissions(['kategori.ekle']);

        $this->actingAs($user)->post(route('kategoriler.store'), ['name' => 'Ofis', 'name_en' => $name])
            ->assertInvalid(['name_en' => $message]);

        $this->assertDatabaseCount('categories', 0);
    }

    public static function invalidEnglishNames(): array
    {
        return [
            'not a string' => [['Office'], 'İngilizce kategori adı metin olmalıdır.'],
            'too long' => [str_repeat('A', 256), 'İngilizce kategori adı en fazla 255 karakter olabilir.'],
        ];
    }

    #[DataProvider('missingEnglishNames')]
    public function test_updating_a_category_preserves_an_omitted_english_name_and_clears_a_blank_name(array $payload, ?string $expectedName): void
    {
        $user = $this->userWithPermissions(['kategori.duzenle']);
        $category = $this->category();
        $category->update(['name_en' => 'Electronics']);

        $this->actingAs($user)->put(route('kategoriler.update', $category), array_merge(['name' => 'Elektronik'], $payload))
            ->assertRedirect(route('anasayfa'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name_en' => $expectedName]);
    }

    public static function missingEnglishNames(): array
    {
        return [
            'omitted' => [[], 'Electronics'],
            'blank' => [['name_en' => ''], null],
        ];
    }

    private function userWithPermissions(array $permissions): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->permissions()->sync(Permission::whereIn('name', $permissions)->get()->mapWithKeys(
            fn (Permission $permission): array => [$permission->id => ['allowed' => true]],
        )->all());

        return $user;
    }

    private function category(string $name = 'Elektronik', string $slug = 'elektronik'): Category
    {
        return Category::create(['name' => $name, 'slug' => $slug]);
    }
}
