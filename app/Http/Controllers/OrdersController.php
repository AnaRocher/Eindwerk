<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\DiscountCode;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function checkout() {
        return view('orders.checkout');
    }

    public function store(Request $request) {
        // Validate the form so that all fields are required.
        // Fill in the form again, and display the error messages.
        // Create a new "order" with the data from the form in the database
        // Make sure the order is linked to the logged in user.
        // Find all products linked to the logged in user (shopping cart)
        // Go through all linked products of a user (shopping cart)
        // Attach the product, with corresponding quantity and size, to the order
        // https://laravel.com/docs/9.x/eloquent-relationships#retrieving-intermediate-table-columns
        // Detach the product from the logged in user at the same time so that the shopping cart becomes empty again

         // BONUS: If there is a discount code in the session, link it to the discount_code_id in the order model
        // Afterwards also remove the discount code from the session

        // BONUS: Send an email to the user notifying them that their order was successful,
        // along with a button or link to the order's show page

         // Redirect to the order's show page and edit the function there

        $validatedData = $request->validate([
            'voornaam' => 'required',
            'achternaam' => 'required',
            'straat' => 'required',
            'huisnummer' => 'required',
            'postcode' => 'required',  
            'woonplaats' => 'required',        
        ]);
       
        $user = User::findOrFail(auth()->id());

        if (session()->has('discount_code')) {
            $discountCode = DiscountCode::where('code', session()->get('discount_code'))->first();
            if ($discountCode) {
                $orderData['discount_code_id'] = $discountCode->id;

                session()->forget('discount_code');
            }   
        }

        $order = new Order([
            'user_id' => auth()->id(),
            'voornaam' => $validatedData['voornaam'],
            'achternaam' => $validatedData['achternaam'],
            'straat' => $validatedData['straat'],
            'huisnummer' => $validatedData['huisnummer'],
            'postcode' => $validatedData['postcode'],  
            'woonplaats' => $validatedData['woonplaats'],       
            'total' => $user->calculateCartTotal(),
        ]);

        $order->save();

        foreach ($user->cartItems() as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price * $item->quantity,
            ]);
        }

        $user->cartItems()->delete();       

        return redirect()->route('orders.show', $order);
    }

    public function index() {
        // Look up all orders of the logged in user. Replace the "range" below with the correct code
        $user_id = auth()->id();
        $orders = Order::where('user_id', $user_id)->get();       

        // Adjust the views so that the correct info of an order is shown in the "order" include file
        return view('orders.index', [
            'orders' => $orders
        ]);
    }

    public function show($id) { 
        // Order $order
        // Secure the order with a GATE so that you can only view your own orders.

         // The ID of an order is sent in the URL. Look up the order from the url.
         // Find the corresponding products of the order below.
        $order = Order::findOrFail($id);
        $this->authorize('view', $order);
        $products = $order->products;

        // Pass the correct data to the view
        // Modify the "order-item" include file so that the details of the order are displayed correctly in the website
        return view('orders.show', [
            'order' => $order,
            'products' => $products
        ]);
    }
}