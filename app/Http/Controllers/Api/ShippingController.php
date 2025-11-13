<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Shipping\ShippingAggregatorService;
use App\Services\Shipping\AddressService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\CartItem;

class ShippingController extends Controller
{
    public function quote(Request $request, ShippingAggregatorService $aggregator, AddressService $addressService)
    {
        $validated = $request->validate([
            'destination_cep' => ['required','regex:/^[0-9\-\.]{8,9}$/'],
            'items' => ['array','required'],
            'items.*.weight' => ['numeric','min:0.01'],
            'items.*.length' => ['numeric','min:1'],
            'items.*.height' => ['numeric','min:1'],
            'items.*.width' => ['numeric','min:1'],
            'items.*.value' => ['numeric','min:0'],
        ]);

        $destCep = $addressService->normalizeCep($validated['destination_cep']);
        if (!$destCep) {
            return response()->json(['success'=>false,'error'=>'CEP inválido'],422);
        }

        $destination = ['cep' => $destCep];
        $origin = ['cep' => setting('correios_cep_origem', '')];

        $packages = [];
        foreach ($validated['items'] as $item) {
            $packages[] = [
                'weight' => $item['weight'],
                'length' => $item['length'],
                'height' => $item['height'],
                'width'  => $item['width'],
                'value'  => $item['value'] ?? 0,
            ];
        }

        $quotes = $aggregator->quotes($origin, $destination, $packages);

        return response()->json([
            'success' => true,
            'destination_cep' => $destCep,
            'quotes' => $quotes,
            'count' => count($quotes),
        ]);
    }

    public function track(string $provider, string $code)
    {
        // For now just stub unified response
        return response()->json([
            'success' => false,
            'provider' => $provider,
            'tracking_code' => $code,
            'error' => 'Tracking endpoint em desenvolvimento'
        ], 501);
    }

    public function addressLookup(string $cep, AddressService $addressService)
    {
        $result = $addressService->lookupCep($cep);
        if (!$result['success']) {
            return response()->json($result, 422);
        }
        return response()->json($result);
    }

    /**
     * Persist selected shipping option in session and for authenticated customers.
     * Expected payload: destination_cep, provider, service_code, service_name, price, delivery_time_text
     */
    public function select(Request $request)
    {
        $validated = $request->validate([
            'destination_cep'   => 'required|string|min:8',
            'provider'          => 'required|string',
            'service_code'      => 'nullable|string',
            'service_name'      => 'required|string',
            'price'             => 'required|numeric|min:0',
            'delivery_time_text'=> 'nullable|string',
        ]);

        // Save in session
        $selection = [
            'destination_cep' => preg_replace('/[^0-9]/','', $validated['destination_cep']),
            'provider'        => $validated['provider'],
            'service_code'    => $validated['service_code'] ?? null,
            'service_name'    => $validated['service_name'],
            'price'           => (float)$validated['price'],
            'delivery_time_text' => $validated['delivery_time_text'] ?? null,
        ];
        Session::put('shipping_selection', $selection);

        // If authenticated customer, persist to DB for future sessions
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            // Avoid mass assignment issues if columns not yet present
            try {
                $customer->shipping_cep = $selection['destination_cep'];
                $customer->shipping_option = $selection;
                $customer->save();
            } catch (\Throwable $e) {
                // Silently ignore if columns not available
            }
        }

        // Compute current cart summary (subtotal + shipping + total)
        [$subtotal, $shipping, $total] = $this->computeCartSummary($selection['price']);

        return response()->json([
            'success' => true,
            'selection' => $selection,
            'summary' => [
                'subtotal' => number_format($subtotal, 2, ',', '.'),
                'shipping' => number_format($shipping, 2, ',', '.'),
                'total'    => number_format($total, 2, ',', '.'),
            ]
        ]);
    }

    /**
     * Return the current shipping selection from session or user.
     */
    public function currentSelection()
    {
        $selection = Session::get('shipping_selection');
        if(!$selection){
            $customer = Auth::guard('customer')->user();
            if($customer && property_exists($customer, 'shipping_option') && $customer->shipping_option){
                $selection = is_array($customer->shipping_option) ? $customer->shipping_option : json_decode($customer->shipping_option, true);
                if($selection){
                    Session::put('shipping_selection', $selection);
                }
            }
        }
        return response()->json([
            'success' => true,
            'selection' => $selection
        ]);
    }

    private function computeCartSummary(float $shippingPrice = 0.0): array
    {
        $subtotal = 0.0;
        $customerId = Auth::guard('customer')->id();
        $sessionId = session('cart_session_id');
        $query = CartItem::query();
        if ($customerId) {
            $query->where('customer_id', $customerId)->where(function($q){$q->whereNull('session_id')->orWhere('session_id','');});
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId)->where(function($q){$q->whereNull('customer_id')->orWhere('customer_id',0);});
        } else {
            // No cart context yet
            return [0.0, $shippingPrice, $shippingPrice];
        }
        $items = $query->get(['quantity','price']);
        foreach($items as $it){ $subtotal += ($it->quantity * $it->price); }
        $total = $subtotal + $shippingPrice;
        return [$subtotal, $shippingPrice, $total];
    }

    /**
     * Get current cart summary including shipping (if a selection exists in session)
     */
    public function summary()
    {
        $selection = Session::get('shipping_selection');
        $shippingPrice = $selection['price'] ?? 0.0;
        [$subtotal, $shipping, $total] = $this->computeCartSummary((float)$shippingPrice);
        return response()->json([
            'success' => true,
            'summary' => [
                'subtotal' => number_format($subtotal, 2, ',', '.'),
                'shipping' => number_format($shipping, 2, ',', '.'),
                'total'    => number_format($total, 2, ',', '.'),
            ],
            'selection' => $selection
        ]);
    }
}
