<?php

namespace App\Repositories;

use App\Models\Company;
use Exception;

class CompanyRepository
{
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Company::with('users')->get();
    }

    public function create(array $data): Company
    {
        $company = Company::create($data);
        
        if (!$company) {
            throw new \Exception('Failed to create company');
        }
        
        return $company;
    }

    public function findById(int $id): ?Company
    {
        return Company::with('users')->find($id);
    }

    public function findOrFail(int $id): Company
    {
        return Company::with('users')->findOrFail($id);
    }
}
