<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private function getStorageFilePath(): string
    {
        return storage_path('app/categories.json');
    }

    private function readCategories(): array
    {
        $filePath = $this->getStorageFilePath();
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

    private function saveCategories(array $categories): void
    {
        $filePath = $this->getStorageFilePath();
        // Ensure directory exists
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }
        file_put_contents($filePath, json_encode(array_values($categories), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function nextId(array $categories): int
    {
        $max = 0;
        foreach ($categories as $cat) {
            $max = max($max, (int)($cat['cat_id'] ?? 0));
        }
        return $max + 1;
    }
    public function index()
    {
        $categories = $this->readCategories();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cat_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image upload
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $imageUrl = Storage::url($path); // \/storage\/categories\/...
        }

        // Persist to JSON storage
        $categories = $this->readCategories();
        $newId = $this->nextId($categories);
        $category = [
            'cat_id' => $newId,
            'cat_name' => $request->cat_name,
            'image' => $imageUrl,
            'description' => $request->description,
            'status' => (bool)$request->boolean('status') || $request->input('status') === '1'
        ];

        $categories[] = $category;
        $this->saveCategories($categories);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cat_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Read existing
        $categories = $this->readCategories();
        $index = null;
        foreach ($categories as $i => $c) {
            if ((string)($c['cat_id'] ?? '') === (string)$id) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category = $categories[$index];
        $category['cat_name'] = $request->cat_name;
        $category['description'] = $request->description;
        $category['status'] = (bool)$request->boolean('status') || $request->input('status') === '1';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $imageUrl = Storage::url($path);
            $category['image'] = $imageUrl;
        }

        $categories[$index] = $category;
        $this->saveCategories($categories);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $categories = $this->readCategories();
        $found = false;
        foreach ($categories as $i => $c) {
            if ((string)($c['cat_id'] ?? '') === (string)$id) {
                // Optionally delete image file from public storage
                if (!empty($c['image']) && is_string($c['image']) && str_starts_with($c['image'], '/storage/')) {
                    $publicPath = str_replace('/storage/', '', $c['image']);
                    if ($publicPath) {
                        Storage::disk('public')->delete($publicPath);
                    }
                }
                unset($categories[$i]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $this->saveCategories(array_values($categories));

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
