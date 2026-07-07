<?php

namespace App\Livewire\Staff\Cafeteria;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\CafeteriaCategory;
use App\Models\CafeteriaProduct;

#[Layout('components.layouts.staff')]
#[Title('Cafeteria Products & Categories')]
class Products extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $categoryFilter = '';
    public $perPage = 10;

    // Category modal/form fields
    public $showCategoryModal = false;
    public $catName = '';
    public $catDescription = '';
    public $selectedCategoryId = null;

    // Product modal/form fields
    public $showProductModal = false;
    public $prodName = '';
    public $prodCode = '';
    public $prodCategoryId = '';
    public $prodPrice = '';
    public $prodWholesalePrice = 0;
    public $prodDistributePrice = 0;
    public $prodStock = 0;
    public $prodImage = '';
    public $prodIsActive = true;
    public $selectedProductId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    // Category Methods
    public function openCategoryModal($id = null)
    {
        $this->resetCategoryForm();
        if ($id) {
            $category = CafeteriaCategory::findOrFail($id);
            $this->selectedCategoryId = $category->id;
            $this->catName = $category->name;
            $this->catDescription = $category->description;
        }
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->resetCategoryForm();
    }

    private function resetCategoryForm()
    {
        $this->selectedCategoryId = null;
        $this->catName = '';
        $this->catDescription = '';
        $this->resetValidation();
    }

    public function saveCategory()
    {
        $this->validate([
            'catName' => 'required|string|max:255',
            'catDescription' => 'nullable|string',
        ]);

        if ($this->selectedCategoryId) {
            $category = CafeteriaCategory::findOrFail($this->selectedCategoryId);
            $category->update([
                'name' => $this->catName,
                'description' => $this->catDescription,
            ]);
            session()->flash('success', 'Category updated successfully.');
        } else {
            CafeteriaCategory::create([
                'name' => $this->catName,
                'description' => $this->catDescription,
            ]);
            session()->flash('success', 'Category created successfully.');
        }

        $this->closeCategoryModal();
    }

    public function deleteCategory($id)
    {
        $category = CafeteriaCategory::findOrFail($id);
        $category->delete();
        session()->flash('success', 'Category and its products deleted successfully.');
        $this->resetPage();
    }

    // Product Methods
    public function openProductModal($id = null)
    {
        $this->resetProductForm();
        if ($id) {
            $product = CafeteriaProduct::findOrFail($id);
            $this->selectedProductId = $product->id;
            $this->prodName = $product->name;
            $this->prodCode = $product->code;
            $this->prodCategoryId = $product->category_id;
            $this->prodPrice = $product->price;
            $this->prodWholesalePrice = $product->wholesale_price;
            $this->prodDistributePrice = $product->distribute_price;
            $this->prodStock = $product->stock;
            $this->prodImage = $product->image;
            $this->prodIsActive = (bool)$product->is_active;
        }
        $this->showProductModal = true;
    }

    public function closeProductModal()
    {
        $this->showProductModal = false;
        $this->resetProductForm();
    }

    private function resetProductForm()
    {
        $this->selectedProductId = null;
        $this->prodName = '';
        $this->prodCode = '';
        $this->prodCategoryId = '';
        $this->prodPrice = '';
        $this->prodWholesalePrice = 0;
        $this->prodDistributePrice = 0;
        $this->prodStock = 0;
        $this->prodImage = '';
        $this->prodIsActive = true;
        $this->resetValidation();
    }

    public function saveProduct()
    {
        $rules = [
            'prodName' => 'required|string|max:255',
            'prodCode' => 'required|string|unique:cafeteria_products,code,' . ($this->selectedProductId ?? 'NULL'),
            'prodCategoryId' => 'required|exists:cafeteria_categories,id',
            'prodPrice' => 'required|numeric|min:0',
            'prodWholesalePrice' => 'nullable|numeric|min:0',
            'prodDistributePrice' => 'nullable|numeric|min:0',
            'prodStock' => 'required|integer|min:0',
            'prodImage' => 'nullable|string',
            'prodIsActive' => 'boolean',
        ];

        $this->validate($rules);

        $data = [
            'category_id' => $this->prodCategoryId,
            'name' => $this->prodName,
            'code' => $this->prodCode,
            'price' => $this->prodPrice,
            'wholesale_price' => $this->prodWholesalePrice ?: 0,
            'distribute_price' => $this->prodDistributePrice ?: 0,
            'stock' => $this->prodStock,
            'image' => $this->prodImage,
            'is_active' => $this->prodIsActive,
        ];

        if ($this->selectedProductId) {
            $product = CafeteriaProduct::findOrFail($this->selectedProductId);
            $product->update($data);
            session()->flash('success', 'Product updated successfully.');
        } else {
            CafeteriaProduct::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->closeProductModal();
    }

    public function deleteProduct($id)
    {
        $product = CafeteriaProduct::findOrFail($id);
        $product->delete();
        session()->flash('success', 'Product deleted successfully.');
        $this->resetPage();
    }

    public function toggleProductStatus($id)
    {
        $product = CafeteriaProduct::findOrFail($id);
        $product->update([
            'is_active' => !$product->is_active
        ]);
        session()->flash('success', 'Product status toggled.');
    }

    public function render()
    {
        $categories = CafeteriaCategory::orderBy('name')->get();

        $query = CafeteriaProduct::with('category');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        $products = $query->latest()->paginate($this->perPage);

        return view('livewire.staff.cafeteria.products', [
            'categories' => $categories,
            'products' => $products
        ]);
    }
}
