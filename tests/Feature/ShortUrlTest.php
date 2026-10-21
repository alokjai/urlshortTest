<?php

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Support\Str;

beforeEach(function () {
    // Refresh database for each test
    $this->artisan('migrate:fresh');

    // Create roles
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'SuperAdmin']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Member']);
});

test('Admin can create short urls', function () {
    $company = Company::create(['name' => 'Test Company']);
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company->id,
    ]);
    $admin->assignRole('Admin');

    actingAs($admin)
        ->get(route('short-urls.create'))
        ->assertStatus(200);

    actingAs($admin)
        ->post(route('short-urls.store'), ['original_url' => 'https://example.com'])
        ->assertRedirect(route('short-urls.index'));
});

test('Member can create short urls', function () {
    $company = Company::create(['name' => 'Test Company']);
    $member = User::create([
        'name' => 'Member User',
        'email' => 'member@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company->id,
    ]);
    $member->assignRole('Member');

    actingAs($member)
        ->get(route('short-urls.create'))
        ->assertStatus(200);

    actingAs($member)
        ->post(route('short-urls.store'), ['original_url' => 'https://example.com'])
        ->assertRedirect(route('short-urls.index'));
});

test('SuperAdmin cannot create short urls', function () {
    $superAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@test.com',
        'password' => bcrypt('password'),
        'company_id' => null,
    ]);
    $superAdmin->assignRole('SuperAdmin');

    actingAs($superAdmin)
        ->get(route('short-urls.create'))
        ->assertStatus(403);

    actingAs($superAdmin)
        ->post(route('short-urls.store'), ['original_url' => 'https://example.com'])
        ->assertStatus(403);
});

test('Admin can only see short urls created in their own company', function () {
    $company1 = Company::create(['name' => 'Company 1']);
    $company2 = Company::create(['name' => 'Company 2']);

    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company1->id,
    ]);
    $admin->assignRole('Admin');

    $user1 = User::create([
        'name' => 'User 1',
        'email' => 'user1@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company1->id,
    ]);
    $user2 = User::create([
        'name' => 'User 2',
        'email' => 'user2@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company2->id,
    ]);

    // Create short URLs
    ShortUrl::create([
        'company_id' => $company1->id,
        'user_id' => $user1->id,
        'original_url' => 'https://example1.com',
        'short_code' => Str::random(10),
    ]);

    ShortUrl::create([
        'company_id' => $company2->id,
        'user_id' => $user2->id,
        'original_url' => 'https://example2.com',
        'short_code' => Str::random(10),
    ]);

    $response = actingAs($admin)
        ->get(route('short-urls.index'))
        ->assertStatus(200);

    // Admin should only see short URLs from their own company (company1)
    $shortUrls = $response->viewData('shortUrls');
    expect($shortUrls)->toHaveCount(1);
    expect($shortUrls->first()->company_id)->toBe($company1->id);
});

test('Member can only see short urls created by themselves', function () {
    $company = Company::create(['name' => 'Test Company']);

    $member = User::create([
        'name' => 'Member User',
        'email' => 'member@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company->id,
    ]);
    $member->assignRole('Member');

    $user1 = User::create([
        'name' => 'User 1',
        'email' => 'user1@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company->id,
    ]);

    // Create short URLs
    ShortUrl::create([
        'company_id' => $company->id,
        'user_id' => $member->id,
        'original_url' => 'https://example1.com',
        'short_code' => Str::random(10),
    ]);

    ShortUrl::create([
        'company_id' => $company->id,
        'user_id' => $user1->id,
        'original_url' => 'https://example2.com',
        'short_code' => Str::random(10),
    ]);

    $response = actingAs($member)
        ->get(route('short-urls.index'))
        ->assertStatus(200);

    // Member should only see short URLs created by themselves
    $shortUrls = $response->viewData('shortUrls');
    expect($shortUrls)->toHaveCount(1);
    expect($shortUrls->first()->user_id)->toBe($member->id);
});

test('Short urls are publicly resolvable and redirect to original url', function () {
    $company = Company::create(['name' => 'Test Company']);
    $user = User::create([
        'name' => 'Test User',
        'email' => 'user@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company->id,
    ]);

    $shortUrl = ShortUrl::create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'original_url' => 'https://example.com',
        'short_code' => 'test123',
    ]);

    // Short URL should be publicly accessible without authentication
    $this->get(route('short-urls.redirect', $shortUrl->short_code))
        ->assertRedirect($shortUrl->original_url);
});

test('SuperAdmin can see all short urls for every company', function () {
    $company1 = Company::create(['name' => 'Company 1']);
    $company2 = Company::create(['name' => 'Company 2']);

    $superAdmin = User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@test.com',
        'password' => bcrypt('password'),
        'company_id' => null,
    ]);
    $superAdmin->assignRole('SuperAdmin');

    $user1 = User::create([
        'name' => 'User 1',
        'email' => 'user1@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company1->id,
    ]);
    $user2 = User::create([
        'name' => 'User 2',
        'email' => 'user2@test.com',
        'password' => bcrypt('password'),
        'company_id' => $company2->id,
    ]);

    // Create short URLs in both companies
    ShortUrl::create([
        'company_id' => $company1->id,
        'user_id' => $user1->id,
        'original_url' => 'https://example1.com',
        'short_code' => Str::random(10),
    ]);

    ShortUrl::create([
        'company_id' => $company2->id,
        'user_id' => $user2->id,
        'original_url' => 'https://example2.com',
        'short_code' => Str::random(10),
    ]);

    $response = actingAs($superAdmin)
        ->get(route('short-urls.index'))
        ->assertStatus(200);

    // SuperAdmin should see all short URLs from all companies
    $shortUrls = $response->viewData('shortUrls');
    expect($shortUrls)->toHaveCount(2);
});
