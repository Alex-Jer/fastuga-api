<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class OrderHelper
{
    // 1 point = 5€
    public const POINTS_TO_EUR = 5.0;

    private static $currTickNumber = 0;

    public static function nextTicketNumber(): int
    {
        if (self::$currTickNumber === 99)
            self::$currTickNumber = 0;
        return self::$currTickNumber++;
    }


    /* ******************************************* */
    /*               PAYMENT SERVICE               */
    /* ******************************************* */

    public static function validatePaymentInfo($type, $reference)
    {
        $err = ['status' => false, 'message' => 'Invalid payment reference'];
        switch (strtolower($type)) {
            case 'visa':
                if (strlen($reference) !== 16 || !is_numeric($reference))
                    return $err;
                else if (startsWith('0'))
                    return $err;
            case 'paypal':
                if (!filter_var($reference, FILTER_VALIDATE_EMAIL))
                    return $err;
            case 'mbway':
                if (strlen($reference) !== 9 || !is_numeric($reference))
                    return $err;
                else if (startsWith('0'))
                    return $err;
            default:
                return $err;
        }
        return ['status' => true];
    }

    public static function processPayment($type, $reference, $value)
    {
        return self::contactPaymentApi('payments', $type, $reference, $value);
    }

    public static function processRefund($type, $reference, $value)
    {
        return self::contactPaymentApi('refunds', $type, $reference, $value);
    }

    private static function contactPaymentApi($action, $type, $reference, $value)
    {
        if ($value <= 0)
            return ['status' => false, 'message' => 'Value must be greater than 0'];
        $resValPay = self::validatePaymentInfo($type, $reference);
        if (!$resValPay['status'])
            return $resValPay;

        $data = array('type' => $type, 'reference' => $reference, 'value' => $value);
        $client = new Client();
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        $body = json_encode($data);
        $request = new Request('POST', 'https://dad-202223-payments-api.vercel.app/api/' . $action, $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $result = json_decode($res->getBody());

        if ($result['status'] !== "valid")
            return [
                'status' => false, 'message' => ($result['message'] == 'invalid reference' ?
                    'Invalid payment reference'
                    : ucfirst($result['message'])
                )
            ];
        //Other possible error messages: Payment limit exceeded

        return ['status' => true];
    }
}
