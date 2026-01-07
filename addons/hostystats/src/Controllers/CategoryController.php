<?php

namespace App\Addons\HostyStats\Controllers;

use App\Addons\HostyStats\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('hostystats::admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('hostystats::admin.categories.form', [
            'category' => new Category(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Category::create($data);

        return redirect()->route('admin.hostystats.categories.index');
    }

    public function edit(Category $category)
    {
        return view('hostystats::admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validateData($request, false);
        $category->update($data);

        return redirect()->route('admin.hostystats.categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.hostystats.categories.index');
    }

    private function validateData(Request $request, bool $creating = true): array
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'description' => ['nullable','string'],
            'position' => ['nullable','integer','min:0'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['position'] = (int)($data['position'] ?? 0);

        
        if ($creating) {
            $data['is_active'] = (bool)($data['is_active'] ?? true);
        } else {
            $data['is_active'] = (bool)($data['is_active'] ?? false);
        }

        return $data;
    }
}
