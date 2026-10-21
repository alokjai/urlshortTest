<?php

namespace App\Repositories;

use App\Models\Invitation;
use Exception;

class InvitationRepository
{
    public function findByToken(string $token): ?Invitation
    {
        return Invitation::where('token', $token)->first();
    }

    public function findPendingByToken(string $token): ?Invitation
    {
        return Invitation::where('token', $token)
            ->where('status', 'pending')
            ->first();
    }

    public function create(array $data): Invitation
    {
        $invitation = Invitation::create($data);
        
        if (!$invitation) {
            throw new Exception('Failed to create invitation');
        }
        
        return $invitation;
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Invitation::with('company', 'invitedBy')->get();
    }

    public function getByCompanyId(int $companyId): \Illuminate\Database\Eloquent\Collection
    {
        return Invitation::where('company_id', $companyId)
            ->with('company', 'invitedBy')
            ->get();
    }

    public function findPendingByEmail(string $email): ?Invitation
    {
        return Invitation::where('email', $email)
            ->where('status', 'pending')
            ->first();
    }

    public function delete(Invitation $invitation): bool
    {
        return $invitation->delete();
    }

    public function updateStatus(Invitation $invitation, string $status): bool
    {
        return $invitation->update(['status' => $status]);
    }
}
