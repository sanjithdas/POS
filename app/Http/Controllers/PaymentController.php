<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

use Milon\Barcode\DNS2D;

class PaymentController extends Controller
{
    public function handlePayment(Request $request)
    {
        try {
            // Set your Stripe secret key
            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            // Create a payment charge
            $charge = Charge::create([
                'amount' => $request->amount * 100, // Amount in cents
                'currency' => 'usd',
                'source' => $request->stripeToken, // Token from the frontend
                'description' => 'Order Payment',
            ]);

            $orderId = $request->orderId;

            // Check if the payment is successful
            if ($charge->status === 'succeeded') {
                // Find the order using the order ID from the request
                $order = Order::findOrFail($request->orderId);

                // Update the order status to 'paid'
                $order->status = 'completed';
                $order->payment_date = now(); // Store the payment timestamp
                $order->payment_type= "card";
                $order->save();

                // Return a successful response
                return response()->json(['success' => 'Payment successful!']);
            } else {
                // Payment failed
                return response()->json(['error' => 'Payment failed. Please try again.']);
            }
        } catch (\Exception $e) {
            // Handle error
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function generateBarcode(Request $request)
    {
        // Example payment data
        $paymentData = [
            'order_id' => $request->orderId,
            'amount' => $request->amount,
        ];

        // Encode data in JSON format (you can use other formats like plain text or a URL)
        $paymentJson = json_encode($paymentData);

        // Generate a QR code (or other types of barcodes like EAN-13)
        $barcode = (new DNS2D)->getBarcodeHTML($paymentJson, 'QRCODE');

        return response()->json(['barcode' => $barcode]);
    }

    public function processBarcodePayment(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'orderId' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0'
        ]);

        try {
            // Fetch the order from the database
            $order = Order::findOrFail($request->orderId);

            // Check if the amount matches the order total (optional)
            if ($order->total_amount != $request->amount) {
                return response()->json(['error' => 'Invalid amount'], 400);
            }

            // Update the order status to 'paid'
            $order->status = 'completed'; // Assuming 'paid' is the status for completed payment
          //  $order->payment_method = 'barcode'; // Optionally track the payment method
            $order->payment_date = now(); // Store the payment timestamp
            $order->payment_type="barcode";
            $order->save();

            // Return success response
            return response()->json(['success' => true, 'message' => 'Payment processed successfully.']);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}