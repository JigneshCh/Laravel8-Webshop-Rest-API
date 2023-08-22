<?php

namespace App\Repositories;

use App\Repositories\PaymentInterface as PaymentInterface;

class PaymentRepository implements PaymentInterface
{

	/**
	 * Super Payment API
	 *
	 * @param array $order
	 * @return array $data
	 */
	public function superPayment($order)
	{

		try {
			$option = [
				'curl'   => [CURLOPT_SSL_VERIFYPEER => false],
				'verify' => false
			];
			$client = new \GuzzleHttp\Client($option);
			$response = $client->request('POST', 'https://superpay.view.agentur-loop.com/pay', [
				'json' => ['order_id' => $order->id, "customer_email" => $order->customer, 'value' => $order->payable_amount]
			]);

			$status_code = $response->getStatusCode();
			$data = json_decode($response->getBody(), true);
			if ($status_code == 200) {
				$result = [
					'status' => 1,
					'message' => (isset($data['message'])) ? $data['message'] : "Payment Successful",
					'data' => $data
				];
			} else {
				$result = [
					'status' => 0,
					'message' => (isset($data['message'])) ? $data['message'] : "Insufficient Funds",
					'data' => $data
				];
			}
		} catch (\Exception $e) {
			$result = [
				'status' => 0,
				'message' => $e->getMessage(),
				'data' => []
			];
		}

		return $result;
	}
}
