<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutAction extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }
    public function index()
    {
        return inertia('Checkout', [
            'str_intent' => $this->user->createSetupIntent(),
            'cashier_key' => config('cashier.key')
        ]);
    }

    public function charge(Request $request)
    {
        if (!$this->user->subscribed('default'))
            $this->user->newSubscription('default', 'price_1PH4DOLVUytZ8PTC9wKUoD4U')->create($request->paymentMethod);


        return redirect()->route('my_contents');
    }
}
