<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Store::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%");
        }

        $query->latest('created_at');

        $stores = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('pages.stores.table', compact('stores'))->render();
        }

        return view('pages.stores.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.stores.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'phone' => 'nullable|max:15',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            Store::create($validated);

            return redirect()->route('stores.index')->with('success', 'Toko berhasil ditambahkan.');
        } catch (\Throwable $th) {
            if (app()->environment('local')) {
                dd($th->getMessage());
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan toko.');
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
        $store = Store::findOrFail($id);
        return view('pages.stores.edit', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|max:15',
                'address' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            $store->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return redirect()->route('stores.index')->with('success', 'Toko berhasil diperbarui.');
        } catch (\Throwable $th) {
            if (app()->environment('local')) {
                dd($th->getMessage());
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan toko.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($store)
    {
        $store = Store::find($store)->delete();

        return redirect()->route('stores.index')->with('success', 'Toko berhasil di hapus.');
    }
}
