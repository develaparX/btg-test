<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    private $apiBaseUrl = 'http://localhost:8080/api/v1';

    public function index()
    {
        try {
            // Fetch customers from Golang API
            $response = Http::get($this->apiBaseUrl . '/customers');
            
            if ($response->successful()) {
                $data = $response->json();
                $customers = $data['data'] ?? [];
            } else {
                $customers = [];
                session()->flash('error', 'Failed to fetch customers from API');
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            $customers = [];
            session()->flash('error', 'Unable to connect to API server');
        }

        return view('users.index', compact('customers'));
    }

    public function create()
    {
        try {
            // Fetch nationalities from Golang API
            $response = Http::get($this->apiBaseUrl . '/nationalities');
            
            if ($response->successful()) {
                $data = $response->json();
                $nationalities = $data['data'] ?? [];
            } else {
                $nationalities = [];
                session()->flash('error', 'Failed to fetch nationalities from API');
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            $nationalities = [];
            session()->flash('error', 'Unable to connect to API server');
        }

        return view('users.create', compact('nationalities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'nationality_id' => 'required|integer',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'family.*.name' => 'nullable|string|max:255',
            'family.*.birth_date' => 'nullable|date',
            'family.*.relation' => 'nullable|string|max:255',
        ]);

        try {
            // Prepare family list data
            $familyList = [];
            if ($request->has('family')) {
                foreach ($request->family as $family) {
                    if (!empty($family['name']) && !empty($family['birth_date']) && !empty($family['relation'])) {
                        $familyList[] = [
                            'name' => $family['name'],
                            'date_of_birth' => $family['birth_date'],
                            'relation' => $family['relation']
                        ];
                    }
                }
            }

            // Prepare data for Golang API
            $apiData = [
                'nationality_id' => (int) $request->nationality_id,
                'name' => $request->name,
                'date_of_birth' => $request->birth_date,
                'phone_number' => $request->phone,
                'email' => $request->email,
                'family_list' => $familyList
            ];

            // Send to Golang API
            $response = Http::post($this->apiBaseUrl . '/customers', $apiData);
            
            if ($response->successful()) {
                return redirect()->route('users.index')->with('success', 'User created successfully!');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error'] ?? 'Failed to create user';
                return back()->withInput()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Unable to connect to API server');
        }
    }

    public function show($id)
    {
        try {
            $response = Http::get($this->apiBaseUrl . "/customers/{$id}");
            
            if ($response->successful()) {
                $data = $response->json();
                $customer = $data['data'] ?? null;
                
                if ($customer) {
                    return view('users.show', compact('customer'));
                }
            }
            
            return redirect()->route('users.index')->with('error', 'Customer not found');
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Unable to connect to API server');
        }
    }

    public function edit($id)
    {
        try {
            // Fetch customer data
            $customerResponse = Http::get($this->apiBaseUrl . "/customers/{$id}");
            $nationalitiesResponse = Http::get($this->apiBaseUrl . '/nationalities');
            
            if ($customerResponse->successful() && $nationalitiesResponse->successful()) {
                $customerData = $customerResponse->json();
                $nationalitiesData = $nationalitiesResponse->json();
                
                $customer = $customerData['data'] ?? null;
                $nationalities = $nationalitiesData['data'] ?? [];
                
                if ($customer) {
                    return view('users.edit', compact('customer', 'nationalities', 'id'));
                }
            }
            
            return redirect()->route('users.index')->with('error', 'Customer not found');
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Unable to connect to API server');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'nationality_id' => 'required|integer',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'family.*.name' => 'nullable|string|max:255',
            'family.*.birth_date' => 'nullable|date',
            'family.*.relation' => 'nullable|string|max:255',
        ]);

        try {
            // Prepare family list data
            $familyList = [];
            if ($request->has('family')) {
                foreach ($request->family as $family) {
                    if (!empty($family['name']) && !empty($family['birth_date']) && !empty($family['relation'])) {
                        $familyList[] = [
                            'name' => $family['name'],
                            'date_of_birth' => $family['birth_date'],
                            'relation' => $family['relation']
                        ];
                    }
                }
            }

            // Prepare data for Golang API
            $apiData = [
                'nationality_id' => (int) $request->nationality_id,
                'name' => $request->name,
                'date_of_birth' => $request->birth_date,
                'phone_number' => $request->phone,
                'email' => $request->email,
                'family_list' => $familyList
            ];

            // Send to Golang API
            $response = Http::put($this->apiBaseUrl . "/customers/{$id}", $apiData);
            
            if ($response->successful()) {
                return redirect()->route('users.index')->with('success', 'User updated successfully!');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error'] ?? 'Failed to update user';
                return back()->withInput()->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Unable to connect to API server');
        }
    }

    public function destroy($id)
    {
        try {
            $response = Http::delete($this->apiBaseUrl . "/customers/{$id}");
            
            if ($response->successful()) {
                return redirect()->route('users.index')->with('success', 'User deleted successfully!');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error'] ?? 'Failed to delete user';
                return redirect()->route('users.index')->with('error', $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('API Error: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Unable to connect to API server');
        }
    }
}