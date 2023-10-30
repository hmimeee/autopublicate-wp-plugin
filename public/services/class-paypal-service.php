<?php

class AP_PayPal_Service
{
    public $url = 'https://api-m.sandbox.paypal.com/v2/';

    public $client;

    public function __construct()
    {
        $this->client = new WP_Http();
    }

    private function request($uri, $args)
    {
        $args = array_merge([
            'method' => 'GET',
            'headers' => [
                'Authorization' => 'Basic Pw4tnlchWaL2Qitro7meBSfePEnh8VwfY26_1RQO:EIeVB-gKDG8lYbOBVPwAsxahfZyNgVkch-h3xcm1Dv4U6D_06bW3XpuMhX2XoWoJgWRMqRQFn-4n-t-B',
                'Content-Type' => 'application/json'
            ]
        ], $args);
        return $this->client->request($this->url . trim($uri, '/'), $args);
    }

    public function checkout($amount, $success_url, $cancel_url = '/')
    {
        $request = $this->request('checkout/orders', [
            'method' => 'POST',
            'body' => json_encode([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => $amount
                    ]
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            'brand_name' => 'Autopublícate®',
                            'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                            'user_action' => 'PAY_NOW',
                            'return_url' => $success_url,
                            'cancel_url' => $cancel_url,
                        ]
                    ]
                ]
            ])
        ]);

        dd($request);
    }
}
