<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    private function getMenusFilePath(): string
    {
        return storage_path('app/menus.json');
    }

    private function readMenus(): array
    {
        $filePath = $this->getMenusFilePath();
        if (!file_exists($filePath)) {
            return [];
        }
        $content = file_get_contents($filePath);
        if ($content === false || trim($content) === '') {
            return [];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private function saveMenus(array $menus): void
    {
        $filePath = $this->getMenusFilePath();
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }
        file_put_contents($filePath, json_encode(array_values($menus), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function nextId(array $menus): int
    {
        $max = 0;
        foreach ($menus as $m) {
            $max = max($max, (int)($m['id'] ?? 0));
        }
        return $max + 1;
    }

    private function readCategories(): array
    {
        $path = storage_path('app/categories.json');
        if (!file_exists($path)) return [];
        $content = file_get_contents($path);
        $data = json_decode($content ?: '[]', true);
        return is_array($data) ? $data : [];
    }

    private function enrichWithCategory(array $menu): array
    {
        $categories = $this->readCategories();
        $found = null;
        foreach ($categories as $c) {
            if ((string)($c['cat_id'] ?? '') === (string)($menu['category_id'] ?? '')) {
                $found = $c;
                break;
            }
        }
        $menu['category'] = $found ? [
            'id' => $found['cat_id'],
            'name' => $found['cat_name']
        ] : null;
        return $menu;
    }

    public function index()
    {
        $menus = $this->readMenus();
        // Enrich each with category
        $menus = array_map(fn($m) => $this->enrichWithCategory($m), $menus);
        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $imageUrl = Storage::url($path);
        }

        $menus = $this->readMenus();
        $newId = $this->nextId($menus);
        $menu = [
            'id' => $newId,
            'category_id' => (int)$request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'ingredients' => $request->ingredients,
            'image' => $imageUrl,
            'price' => (string)$request->price,
            'status' => (bool)$request->boolean('status') || $request->input('status') === '1'
        ];

        $menus[] = $menu;
        $this->saveMenus($menus);

        $menu = $this->enrichWithCategory($menu);

        return response()->json([
            'success' => true,
            'message' => 'Menu created successfully',
            'data' => $menu
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $menus = $this->readMenus();
        $index = null;
        foreach ($menus as $i => $m) {
            if ((string)($m['id'] ?? '') === (string)$id) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }

        $menu = $menus[$index];
        $menu['category_id'] = (int)$request->category_id;
        $menu['name'] = $request->name;
        $menu['description'] = $request->description;
        $menu['ingredients'] = $request->ingredients;
        $menu['price'] = (string)$request->price;
        $menu['status'] = (bool)$request->boolean('status') || $request->input('status') === '1';

        if ($request->hasFile('image')) {
            // optionally delete old file
            if (!empty($menu['image']) && is_string($menu['image']) && str_starts_with($menu['image'], '/storage/')) {
                $publicPath = str_replace('/storage/', '', $menu['image']);
                Storage::disk('public')->delete($publicPath);
            }
            $path = $request->file('image')->store('menus', 'public');
            $menu['image'] = Storage::url($path);
        }

        $menus[$index] = $menu;
        $this->saveMenus($menus);

        $menu = $this->enrichWithCategory($menu);

        return response()->json([
            'success' => true,
            'message' => 'Menu updated successfully',
            'data' => $menu
        ]);
    }

    public function destroy($id)
    {
        $menus = $this->readMenus();
        $found = false;
        foreach ($menus as $i => $m) {
            if ((string)($m['id'] ?? '') === (string)$id) {
                if (!empty($m['image']) && is_string($m['image']) && str_starts_with($m['image'], '/storage/')) {
                    $publicPath = str_replace('/storage/', '', $m['image']);
                    Storage::disk('public')->delete($publicPath);
                }
                unset($menus[$i]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found'
            ], 404);
        }

        $this->saveMenus(array_values($menus));

        return response()->json([
            'success' => true,
            'message' => 'Menu deleted successfully'
        ]);
    }

    public function categoriesList()
    {
        $categories = $this->readCategories();
        // Map to id/name for AdminPages/Menu.vue expectations
        $mapped = array_map(function ($c) {
            return [
                'id' => $c['cat_id'] ?? null,
                'name' => $c['cat_name'] ?? ''
            ];
        }, $categories);

        return response()->json([
            'success' => true,
            'data' => $mapped
        ]);
    }
}


