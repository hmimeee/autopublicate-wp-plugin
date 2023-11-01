<?php

class AP_PayPal_Service
{
    public $url = [
        'test' => 'https://api-m.sandbox.paypal.com/v2/',
        'prod' => 'https://api-m.paypal.com/v2/',
    ];

    public $client;

    public $env;

    public function __construct($env = 'test')
    {
        $this->env = $env;
        $this->client = new WP_Http();
    }

    private function request($uri, $args = [])
    {
        $args = array_merge([
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
                'Authorization' => 'Basic ' . base64_encode("AbA9FGYwbmT-47aizogTkjqmU9fNazXJFel6xIKHPw4tnlchWaL2Qitro7meBSfePEnh8VwfY26_1RQO:EIeVB-gKDG8lYbOBVPwAsxahfZyNgVkch-h3xcm1Dv4U6D_06bW3XpuMhX2XoWoJgWRMqRQFn-4n-t-B"),
                'Content-Type' => 'application/json'
            ]
        ], $args);

        return $this->client->request($this->url[$this->env] . trim($uri, '/'), $args);
    }

    public function checkout($amount, $success_url, $cancel_url = '/')
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
