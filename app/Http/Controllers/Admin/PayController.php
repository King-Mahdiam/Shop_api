<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiResponseController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PayController extends ApiResponseController
{
    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'order_items' => 'required',
            'order_items.*.product_id' => 'required|integer',
            'order_items.*.quantity' => 'required|integer',
            'request_from' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        $totalAmount = 0;
        $deliveryAmount = 0;
        foreach ($request->order_items as $orderItem) {
            $product = Product::findOrFail($orderItem['product_id']);
            if ($product->quantity < $orderItem['quantity']) {
                return $this->errorResponse('The product quantity is incorrect', 422);
            }

            $totalAmount += $product->price * $orderItem['quantity'];
            $deliveryAmount += $product->delivery_amount;
        }

        $payingAmount = $totalAmount + $deliveryAmount;

        $amounts = [
            'totalAmount' => $totalAmount,
            'deliveryAmount' => $deliveryAmount,
            'payingAmount' => $payingAmount,
        ];

        $data = array("merchant_id" => '1f18a5cb-46f2-4949-9418-7f7884c794b5',
            "amount" => $payingAmount .'0',
            "callback_url" => url('/api/verify?amount=' . $payingAmount .'0'),
            "description" => "خرید"
        );
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/request.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v1');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        $err = curl_error($ch);
        $result = json_decode($result, true, JSON_PRETTY_PRINT);
        curl_close($ch);

        if (empty($result['errors'])) {
            if ($result['data']['code'] == 100) {
                $response_token = $result['data']["authority"];
                OrderController::create($request, $amounts, $response_token);
                $go = 'https://www.zarinpal.com/pg/StartPay/' . $response_token;
                return $this->SuccessResponse($go , 200 , 'true');
            }
        } else {
            return $this->ErrorResponse(422 , 'error');
        }
    }

    public function verify(Request $request)
    {
        $Authority = $request->Authority;
        $data = array("merchant_id" => '1f18a5cb-46f2-4949-9418-7f7884c794b5', "authority" => $Authority, "amount" => $request->amount);
        $jsonData = json_encode($data);
        $ch = curl_init('https://api.zarinpal.com/pg/v4/payment/verify.json');
        curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));

        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result, true);

        if (isset($result['data']['code']) && $result['data']['code'] == 100) {
            return $this->SuccessResponse('true' , 200);
        } else {
            return $this->ErrorResponse(422 , 'error');
        }
    }

}
