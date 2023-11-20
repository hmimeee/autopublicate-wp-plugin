<?php

class AP_PayPal_Service
{
    public $url = [
        'test' => 'https://api-m.sandbox.paypal.com/v2/',
        'prod' => 'https://api-m.paypal.com/v2/',
    ];

    public $client;

    private $setting;

    public function __construct()
    {
        $this->client = new WP_Http();
        $this->setting = maybe_unserialize(get_option('ap_payment_settings'));
    }

    private function request($uri, $args = [])
    {
        $clientId = $this->setting['paypal_client_id'] ?? '';
        $clientSecret = $this->setting['paypal_client_secret'] ?? '';
        $args = array_merge([
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret"),
                'Content-Type' => 'application/json'
            ]
        ], $args);

        return $this->client->request($this->url[$this->setting['paypal_environment']] . trim($uri, '/'), $args);
    }

    public function checkout($amount, $success_url, $cancel_url)
    {
        $request = $this->request('checkout/orders', [
            'method' => 'POST',
            'body' => json_encode([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => '123',
                        'amount' => [
                            'currency_code' => 'EUR',
                            'value' => "$amount"
                        ]
                    ]
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            "return_url" => $success_url,
                            "cancel_url" => $cancel_url
                        ]
                    ]
                ]
            ])
        ]);

        return json_decode($request['body'], true);
    }

    public function details($id)
    {
        $request = $this->request('checkout/orders/' . $id);

        return json_decode($request['body'], true);
    }
}
