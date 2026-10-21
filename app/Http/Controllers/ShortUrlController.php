<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShortUrlRequest;
use App\Services\ShortUrlService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShortUrlController extends Controller
{
    public function __construct(
        private ShortUrlService $shortUrlService
    ) {}

    public function index(): \Illuminate\View\View
    {
        try {
            $user = Auth::user();
            $shortUrls = $this->shortUrlService->getShortUrlsByRole($user);

            return view('short-urls.index', compact('shortUrls'));
        } catch (Exception $e) {
            Log::error('Failed to load short URLs', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to load short URLs.');
        }
    }

    public function create(): \Illuminate\View\View
    {
        try {
            $user = Auth::user();

            if ($user->hasRole('SuperAdmin')) {
                abort(403, 'You do not have permission to create short URLs.');
            }

            return view('short-urls.create');
        } catch (Exception $e) {
            Log::error('Failed to load create form', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to load form.');
        }
    }

    public function store(StoreShortUrlRequest $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = Auth::user();

            $this->shortUrlService->createShortUrl(
                $request->original_url,
                $user->id,
                $user->company_id
            );

            return redirect()->route('short-urls.index')->with('success', 'Short URL created successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create short URL', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to create short URL. Please try again.');
        }
    }

    public function redirect(string $shortCode): \Illuminate\Http\RedirectResponse
    {
        try {
            $originalUrl = $this->shortUrlService->redirectShortUrl($shortCode);
            return redirect($originalUrl);
        } catch (Exception $e) {
            Log::error('Failed to redirect short URL', [
                'error' => $e->getMessage(),
                'short_code' => $shortCode,
            ]);
            abort(404, 'Short URL not found or has been deleted.');
        }
    }
}
