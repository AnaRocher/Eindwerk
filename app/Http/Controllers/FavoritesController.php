<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    public function favorites() {
        // Look up only the favorite products of the logged in user
        $user = auth()->user();
        $user = User::find(Auth::id());
        $favorites = $user->favorites()->get();
        $favoritesCount = $favorites->count();
        return view('profile.favorites', [
            'products' => $favorites, 
            'favoritesCount' => $favoritesCount
        ]);
    }

    public function toggleFavorite(Product $product) {
        // Toggle the product id on the "favorites" relationship of the logged in user.
        // https://laravel.com/docs/9.x/eloquent-relationships#toggling-associations

        $user = auth()->user();
        $user = User::find(Auth::id());
     
        if ($user->favorites()->where('product_id', $product->id)->exists()) {
            $user->favorites()->detach($product->id);
        } else {
            $user->favorites()->attach($product->id);
        }
        return back();
    }
}