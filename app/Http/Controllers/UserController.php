<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        // $this->middleware('role:admin')->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {

        $query = User::with(['roles']);

        if ($search = $request->input('search')) {
            $query->where("name", "like", "%$search%");
        }


        $users = $query->paginate(10)->withQueryString();
        // dd($users);
        if ($request->ajax()) {
            return view('pages.users.table', compact('users'))->render();
        }

        return view('pages.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = RoleEnum::cases();
        return view('pages.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'regex:/^[0-9]{10,14}$/'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', new Enum(RoleEnum::class)],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole($validated['role']);

            DB::commit();

            return redirect()->route('users.index')->with('success', 'Pegawai berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (app()->environment('local')) {
                dd($th->getMessage());
            }
            return redirect()->back()->with('error', 'terjadi kesalahan.');
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

        $user = User::find($id);
        $roles = RoleEnum::cases();
        return view('pages.users.edit', compact('roles', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user)
    {

        try {
            $user = User::find($user);

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'phone' => ['required', 'regex:/^[0-9]{10,14}$/'],
                'password' => ['nullable', 'string', 'min:6'],
                'role' => ['required', 'string'],
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            $user->syncRoles([$validated['role']]);

            return redirect()->route('users.index')->with('success', 'Pegawai berhasil diperbarui.');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (app()->environment('local')) {
                dd($th->getMessage());
            }
            return redirect()->back()->with('error', 'terjadi kesalahan.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $user)
    {
        User::find($user)->delete();

        return redirect()->route('users.index')->with('success', 'Pegawai berhasil di hapus.');
    }
}
