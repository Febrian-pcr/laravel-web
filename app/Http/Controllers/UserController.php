<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['dataUser'] = User::all();
		return view('admin.user.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)

        //dd($request->all());
        // $data['name'] = $request->name;
		// $data['email'] = $request->email;
		// $data['password'] = $request->password;

		// User::create($data);

		// return redirect()->route('user.index')->with('success','Penambahan Data Berhasil!');
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('user.index')
                             ->with('success', 'Data user berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withInput()
                         ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['dataUser'] = user::findOrFail($id);
        return view('admin.user.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    // 1. Ambil data user berdasarkan ID
    $user = User::findOrFail($id);

    // 2. Validasi data
    $validated = $request->validate([
        'name'     => ['required', 'string', 'max:100'],
        'email'    => ['required', 'email', 'unique:users,email,' . $user->id],
        'password' => ['nullable', 'string', 'min:8', 'confirmed'],
    ]);

    try {
        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
        ];

        // Hash password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        // 3. Update data
        $user->update($data);

        return redirect()->route('user.index')
                         ->with('success', 'Data user berhasil diperbarui!');
    } catch (\Exception $e) {
        return back()->withInput()
                     ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dataUser = User::findOrFail($id);
        $dataUser->delete();

        return redirect()->route('user.index')->with('success', 'Data user berhasil dihapus.');
    }
}
