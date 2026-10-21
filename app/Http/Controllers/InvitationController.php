<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Services\InvitationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    public function __construct(
        private InvitationService $invitationService
    ) {}

    public function index(): \Illuminate\View\View
    {
        try {
            $user = Auth::user();
            $invitations = $this->invitationService->getInvitationsByRole($user);

            return view('invitations.index', compact('invitations'));
        } catch (Exception $e) {
            Log::error('Failed to load invitations', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to load invitations.');
        }
    }

    public function create(): \Illuminate\View\View
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('SuperAdmin') && !$user->hasRole('Admin')) {
                abort(403, 'You do not have permission to invite users.');
            }

            $companies = Company::all();

            if ($user->hasRole('SuperAdmin')) {
                $availableRoles = ['Admin', 'Member'];
            } else {
                $availableRoles = ['Admin', 'Member'];
            }

            return view('invitations.create', compact('companies', 'availableRoles'));
        } catch (Exception $e) {
            Log::error('Failed to load invitation form', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', 'Failed to load form.');
        }
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = Auth::user();

            if (!$user->hasRole('SuperAdmin') && !$user->hasRole('Admin')) {
                abort(403, 'You do not have permission to invite users.');
            }

            $request->validate([
                'email' => 'required|email',
                'role' => 'required|in:Admin,Member',
                'company_id' => 'nullable|exists:companies,id',
            ]);

            // Admin can only invite to their own company
            if ($user->hasRole('Admin')) {
                $request->merge(['company_id' => $user->company_id]);
            }

            $this->invitationService->createInvitation(
                $request->email,
                $request->role,
                $request->company_id,
                $user->id
            );

            return redirect()->route('invitations.index')->with('success', 'Invitation sent successfully.');
        } catch (Exception $e) {
            Log::error('Failed to send invitation', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return back()->with('error', $e->getMessage() ?: 'Failed to send invitation. Please try again.');
        }
    }

    public function accept(string $token): \Illuminate\Http\RedirectResponse
    {
        try {
            $user = $this->invitationService->acceptInvitation($token);
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Invitation accepted. Please change your password.');
        } catch (Exception $e) {
            Log::error('Failed to accept invitation', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);
            return redirect()->route('login')->with('error', $e->getMessage() ?: 'Invalid or expired invitation.');
        }
    }

    public function destroy(Invitation $invitation): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->invitationService->deleteInvitation($invitation);
            return redirect()->route('invitations.index')->with('success', 'Invitation deleted successfully.');
        } catch (Exception $e) {
            Log::error('Failed to delete invitation', [
                'error' => $e->getMessage(),
                'invitation_id' => $invitation->id,
            ]);
            return back()->with('error', 'Failed to delete invitation.');
        }
    }
}
