<?php

namespace App\Services;

use App\Models\ShortUrl as ShortUrlModel;
use App\Models\User;
use App\Repositories\ShortUrlRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ShortUrlService
{
    public function __construct(
        private ShortUrlRepository $shortUrlRepository
    ) {}

    public function createShortUrl(string $originalUrl, int $userId, ?int $companyId): ShortUrlModel
    {
        try {
            DB::beginTransaction();

            $shortCode = $this->generateUniqueShortCode();

            $shortUrl = $this->shortUrlRepository->create([
                'company_id' => $companyId,
                'user_id' => $userId,
                'original_url' => $this->sanitizeUrl($originalUrl),
                'short_code' => $shortCode,
            ]);

            if (!$shortUrl) {
                throw new \Exception('Failed to create short URL');
            }

            Log::info('Short URL created successfully', [
                'user_id' => $userId,
                'short_code' => $shortCode,
                'company_id' => $companyId,
            ]);

            DB::commit();

            return $shortUrl;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create short URL', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);
            throw $e;
        }
    }

    public function redirectShortUrl(string $shortCode): string
    {
        try {
            $shortUrl = $this->shortUrlRepository->findByShortCode($shortCode);

            if (!$shortUrl) {
                Log::warning('Short URL not found', ['short_code' => $shortCode]);
                throw new \Exception('Short URL not found');
            }

            Log::info('Short URL redirected', [
                'short_code' => $shortCode,
                'original_url' => $shortUrl->original_url,
            ]);

            return $shortUrl->original_url;
        } catch (Exception $e) {
            Log::error('Failed to redirect short URL', [
                'error' => $e->getMessage(),
                'short_code' => $shortCode,
            ]);
            throw $e;
        }
    }

    public function getShortUrlsByRole(User $user): \Illuminate\Database\Eloquent\Collection
    {
        try {
            if ($user->hasRole('SuperAdmin')) {
                return $this->shortUrlRepository->getAll();
            }

            if ($user->hasRole('Admin')) {
                return $this->shortUrlRepository->getByCompanyId($user->company_id ?? 0);
            }

            if ($user->hasRole('Member')) {
                return $this->shortUrlRepository->getByUserId($user->id);
            }

            return collect();
        } catch (Exception $e) {
            Log::error('Failed to get short URLs by role', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return collect();
        }
    }

    private function generateUniqueShortCode(): string
    {
        do {
            $shortCode = Str::random(10);
        } while ($this->shortUrlRepository->existsByShortCode($shortCode));

        return $shortCode;
    }

    private function sanitizeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}
