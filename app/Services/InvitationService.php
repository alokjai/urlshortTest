<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\User;
use App\Repositories\InvitationRepository;
use App\Repositories\CompanyRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvitationService
{
    public function __construct(
        private InvitationRepository $invitationRepository,
        private CompanyRepository $companyRepository
    ) {}

    public function createInvitation(string $email, string $role, ?int $companyId, int $invitedBy): Invitation
    {
        try {
            DB::beginTransaction();

            $existingInvitation = $this->invitationRepository->findPendingByEmail($email);
            if ($existingInvitation) {
                Log::warning('Invitation already exists', ['email' => $email]);
                throw new \Exception('Pending invitation already exists for this email.');
            }

            if (!$companyId) {
                $company = $this->companyRepository->create(['name' => 'New Company']);
                $companyId = $company->id;
            }

            $invitation = $this->invitationRepository->create([
                'company_id' => $companyId,
                'invited_by' => $invitedBy,
                'email' => $this->sanitizeEmail($email),
                'role' => $role,
                'token' => Str::random(64),
                'status' => 'pending',
            ]);

            if (!$invitation) {
                throw new \Exception('Failed to create invitation');
            }

            Log::info('Invitation created successfully', [
                'email' => $email,
                'role' => $role,
                'company_id' => $companyId,
            ]);

            DB::commit();

            return $invitation;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create invitation', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
            throw $e;
        }
    }

    public function acceptInvitation(string $token): User
    {
        try {
            DB::beginTransaction();

            $invitation = $this->invitationRepository->findPendingByToken($token);

            if (!$invitation) {
                Log::warning('Invalid or expired invitation', ['token' => $token]);
                throw new \Exception('Invalid or expired invitation.');
            }

            // Check if user already exists
            $existingUser = User::where('email', $invitation->email)->first();
            if ($existingUser) {
                Log::warning('User already exists', ['email' => $invitation->email]);
                throw new \Exception('User with this email already exists.');
            }

            $password = 'password123';

            $user = User::create([
                'name' => $this->generateNameFromEmail($invitation->email),
                'email' => $invitation->email,
                'password' => bcrypt($password),
                'company_id' => $invitation->company_id,
            ]);

            if (!$user) {
                throw new \Exception('Failed to create user');
            }

            $user->assignRole($invitation->role);

            $this->invitationRepository->updateStatus($invitation, 'accepted');

            Log::info('Invitation accepted successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to accept invitation', [
                'error' => $e->getMessage(),
                'token' => $token,
            ]);
            throw $e;
        }
    }

    public function getInvitationsByRole(User $user): \Illuminate\Database\Eloquent\Collection
    {
        try {
            if ($user->hasRole('SuperAdmin')) {
                return $this->invitationRepository->getAll();
            }

            return $this->invitationRepository->getByCompanyId($user->company_id ?? 0);
        } catch (Exception $e) {
            Log::error('Failed to get invitations', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return collect();
        }
    }

    public function deleteInvitation(Invitation $invitation): bool
    {
        try {
            $result = $this->invitationRepository->delete($invitation);

            Log::info('Invitation deleted', [
                'invitation_id' => $invitation->id,
                'email' => $invitation->email,
            ]);

            return $result;
        } catch (Exception $e) {
            Log::error('Failed to delete invitation', [
                'error' => $e->getMessage(),
                'invitation_id' => $invitation->id,
            ]);
            return false;
        }
    }

    private function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    private function generateNameFromEmail(string $email): string
    {
        return explode('@', $email)[0];
    }
}
