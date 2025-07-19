<?php

namespace App\Exports;

use App\Models\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromView;

class StoresExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Store::query();

        if ($search = $this->request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return view('exports.stores', [
            'stores' => $query->get(),
        ]);
    }
}
