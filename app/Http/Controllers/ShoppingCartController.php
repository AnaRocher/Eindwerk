<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ShoppingCartController extends Controller
{
    public function index() {
          // Modify the "cart-item" include file to include the "$product->pivot->quantity" in the form value
         // and the size is also printed with "$product->pivot->size".
         // Make sure you include the correct fields with the relationship in the User model
         // Also make sure that the price calculation in the "cart-item" is correct.
         
        $products = auth()->user()->orders;
          
        // Look up the products of the logged in user.

        $shipping = 3.9;
            // DO THE CALCULATION AS THE LAST STEP
            // Use the "products" relation on the user model (and data pivot table) to go through the products
            // and calculate the full price of the shopping cart.
        $subtotal = 0;
        foreach ($products as $product) {
            $subtotal += $product->price * $product->pivot->quantity;
        }

        // Calculate the shipping costs of 3.9eur in the total
        $total = $subtotal + $shipping;    

        // BONUS: If the discount code exists in the session, look it up in the database and apply the discount to the calculation.
         // You can also send the discount code to the view below.
         // In the index view below you can show the piece in comment code with the correct data.
         // If a code has already been filled in, set the input in the discount-code view file to "disabled"
        $discountAmount = 0;
        $discountCode = false;

        if (session()->has('discountCode')) {
            $discountCode = session()->get('discountCode');
            $coupon = DiscountCode::where('code', $discountCode)->first();
    
            if ($coupon) {
                $discountAmount = $subtotal * ($coupon->percent_off / 100);
                $total = $subtotal - $discountAmount;
            }
        }

        return view('cart.index', [
            'products' => $products,
            'shipping' => $shipping,
            'subtotal' => $subtotal,
            'total' => $total,
            'discountCode' => $discountCode,
            'discountAmount' => $discountAmount
        ]);
    }

    public function add(Request $request, Product $product) {     
        // Insert a control query so that you can only add each product_id to the cart once
         // "Attach" the product to the logged in user
         // Add the size and quantity data from the form to the "pivot" table

         $user = auth()->user();

         if ($user->order && $product && $user->orders->contains($product->id)) {

            return redirect()->route('cart')->with('success', 'The product has been added to your shopping cart');
        }
        $request->user()->orders()->attach($product);
        $request->user()->orders()->updateExistingPivot($product->id, [
            'size' => $request->input('size'),
            'quantity' => $request->input('quantity')
        ]);

        return redirect()->route('cart')->with('success', 'The product has been added to your shopping cart');
    }

    public function removeFromCart(Product $product, User $user) {
        // "Detach" the product from the logged in user
        // https://laravel.com/docs/9.x/eloquent-relationships#attaching-detaching

        if (!$product) {
            throw new InvalidArgumentException('Product cannot be null.');
        }
    
        $user->cart()->detach($product->id); 
        return redirect()->route('cart')->with('success', 'The product has been removed from your shopping cart.');
    }

    public function update(Request $request, Product $product, $id, User $user) {
        // Update the pivot table data with the product id
        // https://laravel.com/docs/9.x/eloquent-relationships#updating-a-record-on-the-intermediate-table

        $order = Order::findOrFail($request->input('order_id'));
        $order->status = $request->input('status');
        $order->save();

        $user->orders()->attach($order);
        $user->load('orders');

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'required|integer',
        ]);
        
        $user = auth()->user();       
        $product = Product::findOrFail($id);
        $order->products()->syncWithoutDetaching([$product->id => ['quantity' => $request->input('quantity'), 'size' => $request->input('size')]]);
    
        return redirect()->route('cart')->with('success', 'The product in your shopping cart has been updated.');
    }

    /**
     * BONUS: DISCOUNTS
     */

    public function setDiscountCode(Request $request) {
        // Validate the form (field is required) and fill it back in if there are any errors

        $discountCode = $request->input('discount_code');

        // BONUS
        // Look up the discount code in the database that contains the CODE field from the request
        // If the discount code was found:
             // Save the discount code to the session for later use at checkout
            // https://laravel.com/docs/9.x/session#storing-data

            $request->validate([
                'discount_code' => 'required'
            ]);

            $discount = DiscountCode::where('CODE', $discountCode)->first();

            if ($discount) {
                session()->put('discount_code', $discount->CODE);              
                    return redirect()->route('cart');
                } else {
                    return back()->withErrors(['discount_code' => 'Invalid discount code']);
                }
        // If the discount code was not found: return with an error that the code could not be found
    }

    public function removeDiscountCode() {
        // Remove the discount code from the session
        session()->forget('discount_code');

        return back();
    }
}