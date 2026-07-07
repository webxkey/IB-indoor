<?php

namespace App\Livewire\Staff\Cafeteria;

use Livewire\Component;
use App\Models\CafeteriaCategory;
use App\Models\CafeteriaProduct;
use App\Models\CafeteriaSale;
use App\Models\CafeteriaSaleItem;
use App\Models\UserUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.staff')]
#[Title('POS Billing Terminal')]
class Billing extends Component
{
    // Search & Filters
    public $search = '';
    public $searchResults = [];
    public $selectedCategory = null;
    public $showCategoryPanel = false;

    // Cart State
    public $cart = [];
    public $priceType = 'retail'; // retail, wholesale, distribute
    public $warrantyThreshold = 5000;

    // Discount Modal
    public $showDiscountModal = false;
    public $additionalDiscount = 0;
    public $additionalDiscountType = 'fixed'; // fixed, percentage

    // Customer & Payment Info
    public $customerId = '';
    public $walkingName = '';
    public $walkingPhone = '';
    public $paymentMethod = 'cash';
    public $paymentStatus = 'paid';

    // Modals
    public $showCustomerModal = false;
    public $newCustomerFirstName = '';
    public $newCustomerLastName = '';
    public $newCustomerPhone = '';
    public $newCustomerEmail = '';

    // Receipt Print Modal
    public $showReceiptModal = false;
    public $completedSale = null;

    public function mount()
    {
        // Set default customer (Walking Customer)
        $this->customerId = '';
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }

        $query = CafeteriaProduct::where('is_active', true)
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });

        $this->searchResults = $query->limit(10)->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'stock' => $product->stock,
                'price' => $this->getProductPriceForType($product),
                'image' => $product->image,
                'pending' => 0
            ];
        })->toArray();
    }

    public function updatedPriceType()
    {
        foreach ($this->cart as $key => $item) {
            if (empty($item['is_custom'])) {
                $product = CafeteriaProduct::find($item['id']);
                if ($product) {
                    $newPrice = $this->getProductPriceForType($product);
                    $this->cart[$key]['price'] = $newPrice;
                    $this->recalculateItemTotal($key);
                }
            }
        }
    }

    private function getProductPriceForType($product)
    {
        if ($this->priceType === 'wholesale') {
            return $product->wholesale_price ?: $product->price;
        } elseif ($this->priceType === 'distribute') {
            return $product->distribute_price ?: $product->price;
        }
        return $product->price;
    }

    public function addToCart($productData)
    {
        $id = $productData['id'];
        $cartKey = 'prod_' . $id;

        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['quantity'] < $this->cart[$cartKey]['stock']) {
                $this->cart[$cartKey]['quantity']++;
                $this->recalculateItemTotal($cartKey);
            } else {
                session()->flash('error', 'Cannot add more. Not enough stock available.');
            }
        } else {
            $product = CafeteriaProduct::findOrFail($id);
            if ($product->stock > 0) {
                $this->cart[$cartKey] = [
                    'key' => $cartKey,
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'price' => $this->getProductPriceForType($product),
                    'quantity' => 1,
                    'stock' => $product->stock,
                    'image' => $product->image,
                    'discount_percentage' => 0,
                    'discount' => 0,
                    'total' => $this->getProductPriceForType($product),
                    'is_custom' => false
                ];
            } else {
                session()->flash('error', 'Product is out of stock.');
            }
        }

        $this->search = '';
        $this->searchResults = [];
        $this->dispatch('product-added-to-cart');
    }

    public function addCustomProduct()
    {
        $cartKey = 'custom_' . Str::random(8);
        $this->cart[$cartKey] = [
            'key' => $cartKey,
            'id' => null,
            'name' => 'Custom Product',
            'code' => 'CUSTOM',
            'price' => 0.00,
            'quantity' => 1,
            'stock' => 9999,
            'image' => '',
            'discount_percentage' => 0,
            'discount' => 0,
            'total' => 0.00,
            'is_custom' => true
        ];
    }

    public function updateCustomName($key, $name)
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['name'] = $name;
        }
    }

    public function updateCustomCode($key, $code)
    {
        if (isset($this->cart[$key])) {
            $this->cart[$key]['code'] = $code;
        }
    }

    public function updateQuantity($key, $quantity)
    {
        $qty = intval($quantity);
        if ($qty <= 0) {
            $qty = 1;
        }

        if (isset($this->cart[$key])) {
            $maxStock = $this->cart[$key]['stock'];
            if ($qty > $maxStock) {
                $qty = $maxStock;
                session()->flash('error', 'Adjusted quantity to maximum available stock.');
            }
            $this->cart[$key]['quantity'] = $qty;
            $this->recalculateItemTotal($key);
        }
    }

    public function incrementQuantity($key)
    {
        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['quantity'] < $this->cart[$key]['stock']) {
                $this->cart[$key]['quantity']++;
                $this->recalculateItemTotal($key);
            }
        }
    }

    public function decrementQuantity($key)
    {
        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['quantity'] > 1) {
                $this->cart[$key]['quantity']--;
                $this->recalculateItemTotal($key);
            }
        }
    }

    public function updatePrice($key, $price)
    {
        $prc = floatval($price);
        if ($prc < 0) {
            $prc = 0;
        }

        if (isset($this->cart[$key])) {
            $this->cart[$key]['price'] = $prc;
            $this->recalculateItemTotal($key);
        }
    }

    public function updateDiscount($key, $discountExpr)
    {
        if (isset($this->cart[$key])) {
            $discountExpr = trim($discountExpr);
            if (str_ends_with($discountExpr, '%')) {
                $percent = floatval(rtrim($discountExpr, '%'));
                $percent = max(0, min(100, $percent));
                $this->cart[$key]['discount_percentage'] = $percent;
            } else {
                $amt = floatval($discountExpr);
                $amt = max(0, $amt);
                $price = $this->cart[$key]['price'];
                $percent = $price > 0 ? ($amt / $price) * 100 : 0;
                $this->cart[$key]['discount_percentage'] = min(100, $percent);
            }
            $this->recalculateItemTotal($key);
        }
    }

    private function recalculateItemTotal($key)
    {
        if (isset($this->cart[$key])) {
            $price = $this->cart[$key]['price'];
            $qty = $this->cart[$key]['quantity'];
            $discountPercent = $this->cart[$key]['discount_percentage'];

            $discountVal = ($price * $discountPercent) / 100;
            $this->cart[$key]['discount'] = $discountVal;

            $itemSubtotal = ($price - $discountVal) * $qty;
            $this->cart[$key]['total'] = $itemSubtotal;
        }
    }

    public function removeFromCart($key)
    {
        if (isset($this->cart[$key])) {
            unset($this->cart[$key]);
        }
    }

    // Global Discount
    public function openSaleDiscountModal()
    {
        $this->showDiscountModal = true;
    }

    public function applyGlobalDiscount()
    {
        $this->showDiscountModal = false;
    }

    public function getAdditionalDiscountAmountProperty()
    {
        $originalSubtotal = collect($this->cart)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        });

        if ($this->additionalDiscountType === 'percentage') {
            return ($originalSubtotal * floatval($this->additionalDiscount)) / 100;
        }
        return floatval($this->additionalDiscount);
    }

    public function getGrandTotalProperty()
    {
        $subtotal = collect($this->cart)->sum('total');
        $globalDiscount = $this->additionalDiscountAmount;
        return max(0, $subtotal - $globalDiscount);
    }

    // Customers
    public function openCustomerModal()
    {
        $this->newCustomerFirstName = '';
        $this->newCustomerLastName = '';
        $this->newCustomerPhone = '';
        $this->newCustomerEmail = '';
        $this->showCustomerModal = true;
    }

    public function closeCustomerModal()
    {
        $this->showCustomerModal = false;
    }

    public function saveCustomer()
    {
        $this->validate([
            'newCustomerFirstName' => 'required|string|max:255',
            'newCustomerLastName' => 'required|string|max:255',
            'newCustomerPhone' => 'required|string|max:20',
            'newCustomerEmail' => 'nullable|email|unique:users_user,email',
        ]);

        $customer = UserUser::create([
            'first_name' => $this->newCustomerFirstName,
            'last_name' => $this->newCustomerLastName,
            'email' => $this->newCustomerEmail,
            'phone_number' => $this->newCustomerPhone,
            'username' => 'caf_' . strtolower($this->newCustomerFirstName) . '_' . rand(100, 999),
            'password' => bcrypt(Str::random(16)),
            'is_active' => true,
            'is_staff' => false,
            'is_superuser' => false,
        ]);

        $this->customerId = $customer->id;
        $this->closeCustomerModal();
        session()->flash('success', 'Customer registered successfully.');
    }

    // Toggle Panels
    public function toggleCategoryPanel()
    {
        $this->showCategoryPanel = !$this->showCategoryPanel;
    }

    public function selectCategory($catId)
    {
        $this->selectedCategory = $catId;
        $this->showCategoryPanel = false;
    }

    // Checkout
    public function validateAndCreateSale()
    {
        if (count($this->cart) === 0) {
            session()->flash('error', 'Cart is empty.');
            return;
        }

        $billingNo = 'CAF-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        DB::beginTransaction();

        try {
            foreach ($this->cart as $key => $item) {
                if (empty($item['is_custom'])) {
                    $product = CafeteriaProduct::findOrFail($item['id']);
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Product '{$product->name}' is out of stock (Available: {$product->stock}).");
                    }
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $originalSubtotal = collect($this->cart)->sum(function ($item) {
                return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
            });
            $unitDiscountRs = collect($this->cart)->sum(function ($item) {
                return ($item['discount'] ?? 0) * ($item['quantity'] ?? 0);
            });
            $globalDiscountAmount = $this->additionalDiscountAmount;
            $totalDiscountAmount = $unitDiscountRs + $globalDiscountAmount;

            $custName = null;
            $custPhone = null;
            $dbCustomerId = null;

            if ($this->customerId !== '') {
                $customerObj = UserUser::find($this->customerId);
                if ($customerObj) {
                    $dbCustomerId = $customerObj->id;
                    $custName = $customerObj->first_name . ' ' . $customerObj->last_name;
                    $custPhone = $customerObj->phone_number;
                }
            } else {
                $custName = $this->walkingName ?: 'Walking Customer';
                $custPhone = $this->walkingPhone ?: '';
            }

            $sale = CafeteriaSale::create([
                'billing_no' => $billingNo,
                'user_id' => auth()->id(),
                'customer_id' => $dbCustomerId,
                'customer_name' => $custName,
                'customer_phone' => $custPhone,
                'subtotal' => $originalSubtotal,
                'discount_amount' => $totalDiscountAmount,
                'grand_total' => $this->grandTotal,
                'payment_method' => $this->paymentMethod,
                'payment_status' => $this->paymentStatus,
                'price_type' => $this->priceType,
            ]);

            foreach ($this->cart as $key => $item) {
                CafeteriaSaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_code' => $item['code'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount_percentage' => $item['discount_percentage'],
                    'subtotal' => $item['total'],
                ]);
            }

            DB::commit();

            $this->cart = [];
            $this->additionalDiscount = 0;
            $this->additionalDiscountType = 'fixed';
            $this->walkingName = '';
            $this->walkingPhone = '';

            $this->completedSale = CafeteriaSale::with('items')->find($sale->id);
            $this->showReceiptModal = true;

            session()->flash('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to complete checkout: ' . $e->getMessage());
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->completedSale = null;
    }

    public function getImageUrl($image)
    {
        if (empty($image)) {
            return 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrn_80I-lMAa0pVBNmFmQ7VI6l4rr74JW-eQ&s';
        }
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }
        return asset('storage/' . $image);
    }

    public function goToDashboard()
    {
        return redirect()->route('staff.dashboard');
    }

    public function render()
    {
        $customersList = UserUser::orderBy('first_name')->limit(50)->get();

        $gridQuery = CafeteriaProduct::where('is_active', true);
        if ($this->selectedCategory) {
            $gridQuery->where('category_id', $this->selectedCategory);
        }
        $gridProducts = $gridQuery->orderBy('name')->get()->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'stock' => $product->stock,
                'price' => $this->getProductPriceForType($product),
                'image' => $product->image,
                'pending' => 0
            ];
        })->toArray();

        $categoriesList = CafeteriaCategory::orderBy('name')->get();

        $selectedCustomerObj = null;
        $customerOpeningBalance = 0;
        if ($this->customerId !== '') {
            $selectedCustomerObj = UserUser::find($this->customerId);
        }

        return view('livewire.staff.cafeteria.billing', [
            'customers' => $customersList,
            'selectedCustomer' => $selectedCustomerObj,
            'customerOpeningBalanceDisplay' => $customerOpeningBalance,
            'gridProducts' => $gridProducts,
            'categories' => $categoriesList,
        ]);
    }
}
