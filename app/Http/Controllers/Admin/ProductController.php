<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['subcategory.category', 'company', 'primaryImage']);

        // Filter by subcategory
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        // Filter by company
        if ($request->filled('company_id')) {
            if ($request->company_id === 'global') {
                $query->where('is_global', true);
            } else {
                $query->where('company_id', $request->company_id);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        $subcategories = Subcategory::with('category')->orderBy('name')->get();
        $companies = User::where('role', 'business')->orderBy('business_name')->get();

        return view('admin.products.index', compact('products', 'subcategories', 'companies'));
    }

    public function create()
    {
        $subcategories = Subcategory::with('category')->orderBy('name')->get();
        $companies = User::where('role', 'business')->orderBy('business_name')->get();

        return view('admin.products.create', compact('subcategories', 'companies'));
    }

    public function store(Request $request)
    {
       // dd($request->all()); // Debugging line to check request data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subcategory_id' => 'required|exists:subcategories,id',
            'company_id' => 'nullable|exists:users,id',
            'is_global' => 'nullable',
            'price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'stock_quantity' => 'nullable|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
        ]);

        $product = Product::create([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'subcategory_id' => $request->subcategory_id,
            'company_id' => ($request->input('is_global') ? null : $request->company_id),
            'is_active' => $request->has('is_active'),
            'is_global' => filter_var($request->input('is_global'), FILTER_VALIDATE_BOOLEAN),
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'specifications' => $request->specifications,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $this->uploadImages($product, $request->file('images'));
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        $product->load(['subcategory.category', 'company', 'images']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['subcategory.category', 'company', 'images']);
        $subcategories = Subcategory::with('category')->orderBy('name')->get();
        $companies = User::where('role', 'business')->orderBy('business_name')->get();

        return view('admin.products.edit', compact('product', 'subcategories', 'companies'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subcategory_id' => 'required|exists:subcategories,id',
            'company_id' => 'nullable|exists:users,id',
            'is_global' => 'boolean',
            'price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'stock_quantity' => 'nullable|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'specifications' => 'nullable|array',
        ]);

        $product->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'subcategory_id' => $request->subcategory_id,
            'company_id' => $request->is_global ? null : $request->company_id,
            'is_active' => $request->has('is_active'),
            'is_global' => $request->has('is_global'),
            'price' => $request->price,
            'sku' => $request->sku,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'specifications' => $request->specifications,
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $this->uploadImages($product, $request->file('images'));
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete associated images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        $status = $product->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.products.index')
            ->with('success', "Product {$status} successfully!");
    }

    public function deleteImage(ProductImage $image)
    {
        // Don't delete if it's the only image
        if ($image->product->images()->count() <= 1) {
            return back()->with('error', 'Cannot delete the only image. Please add another image first.');
        }

        // If this is the primary image, make the first remaining image primary
        if ($image->is_primary) {
            $nextImage = $image->product->images()->where('id', '!=', $image->id)->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        // Delete from storage
        Storage::disk('public')->delete($image->image_path);

        // Delete from database
        $image->delete();

        return back()->with('success', 'Image deleted successfully!');
    }

    public function setPrimaryImage(ProductImage $image)
    {
        // Remove primary from all other images of this product
        $image->product->images()->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated successfully!');
    }

    private function uploadImages($product, $images)
    {
        $isFirst = $product->images()->count() === 0;

        foreach ($images as $index => $image) {
            $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products/' . $product->id, $filename, 'public');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'alt_text' => $product->title . ' - Image ' . ($index + 1),
                'is_primary' => $isFirst && $index === 0, // First image of first upload becomes primary
                'sort_order' => $product->images()->count() + $index,
            ]);
        }
    }

    // API methods for Unity WebGL frontend
    public function apiIndex(Request $request)
    {
        $query = Product::with(['subcategory.category', 'primaryImage'])->active();

        // Filter by company
        if ($request->filled('company_id')) {
            $query->byCompany($request->company_id);
        }

        // Filter by subcategory
        if ($request->filled('subcategory_id')) {
            $query->bySubcategory($request->subcategory_id);
        }

        $products = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function apiShow(Product $product)
    {
        $product->load(['subcategory.category', 'images']);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}
