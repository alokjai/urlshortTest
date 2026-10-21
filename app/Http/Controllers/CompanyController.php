<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyRepository $companyRepository
    ) {}

    public function index(): \Illuminate\View\View
    {
        try {
            $companies = $this->companyRepository->getAll();
            return view('companies.index', compact('companies'));
        } catch (Exception $e) {
            Log::error('Failed to load companies', [
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to load companies.');
        }
    }

    public function create(): \Illuminate\View\View
    {
        try {
            return view('companies.create');
        } catch (Exception $e) {
            Log::error('Failed to load company form', [
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to load form.');
        }
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $this->companyRepository->create([
                'name' => $request->name,
            ]);

            Log::info('Company created successfully', [
                'name' => $request->name,
            ]);

            return redirect()->route('companies.index')->with('success', 'Company created successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create company', [
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to create company. Please try again.');
        }
    }

    public function show(int $id): \Illuminate\View\View
    {
        try {
            $company = $this->companyRepository->findOrFail($id);
            return view('companies.show', compact('company'));
        } catch (Exception $e) {
            Log::error('Failed to load company', [
                'error' => $e->getMessage(),
                'company_id' => $id,
            ]);
            return back()->with('error', 'Company not found.');
        }
    }
}
