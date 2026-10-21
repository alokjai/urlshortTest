<?php

namespace App\Repositories;

use App\Models\ShortUrl;
use Exception;

class ShortUrlRepository
{
    public function findByShortCode(string $shortCode): ?ShortUrl
    {
        return ShortUrl::where('short_code', $shortCode)->first();
    }

    public function create(array $data): ShortUrl
    {
        $shortUrl = ShortUrl::create($data);
        
        if (!$shortUrl) {
            throw new Exception('Failed to create short URL');
        }
        
        return $shortUrl;
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return ShortUrl::with('company', 'user')->get();
    }

    public function getByCompanyId(int $companyId): \Illuminate\Database\Eloquent\Collection
    {
        return ShortUrl::where('company_id', $companyId)
            ->with('company', 'user')
            ->get();
    }

    public function getByUserId(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return ShortUrl::where('user_id', $userId)
            ->with('company', 'user')
            ->get();
    }

    public function existsByShortCode(string $shortCode): bool
    {
        return ShortUrl::where('short_code', $shortCode)->exists();
    }
}
