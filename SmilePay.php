<?php

namespace App\Extensions\Gateways\SmilePay;

use App\Classes\Extensions\Gateway;
use App\Helpers\ExtensionHelper;
use Illuminate\Http\Request;

class SmilePay extends Gateway
{
    /**
    * Get the extension metadata
    * 
    * @return array
    */
    public function getMetadata()
    {
        return [
            'display_name' => 'SmilePay',
            'version' => '1.0.0',
            'author' => 'MineCloud',
            'website' => 'https://mcloudtw.com/',
        ];
    }

    /**
     * Get all the configuration for the extension
     * 
     * @return array
     */
    public function getConfig()
    {
        return [
            [
                'name' => 'api_key',
                'type' => 'text',
                'friendlyName' => 'Api Key',
                'required' => true,
            ]
        ];
    }
    
    /**
     * Get the URL to redirect to
     * 
     * @param int $total
     * @param array $products
     * @param int $invoiceId
     * @return string
     */
    public function pay($total, $products, $invoiceId)
    {
        return [];
    }

    public function webhook(Request $request)
    {
        $apiKeyRequest = $request->header('x-api-key');
        $orderId = $request->header('x-order-id');

        $apiKeyCorrect = ExtensionHelper::getConfig('SmilePay', 'api_key');

        if ($apiKeyRequest !== $apiKeyCorrect) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        if (!$orderId) {
            return response()->json(['error' => 'Missing order ID'], 400);
        }

        try {
            ExtensionHelper::paymentDone($orderId, 'SmilePay', $orderId);
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
