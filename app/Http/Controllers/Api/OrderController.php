<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\PaymentInterface as PaymentInterface;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
	/**
	 * Define API responce structure
	 * @var array
	 */
	private $result = ["data" => [], "status_code" => 400, "message" => "", "validation_errors" => []];

	/**
	 * pay
	 * @var string
	 */
	private $pay;

	public function __construct(PaymentInterface $pay)
	{
		$this->pay = $pay;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return json $data
	 */
	public function index(Request $request)
	{
		$items = Order::orderby('id', 'DESC');
		if ($request->has('paid')) {
			$items->where('paid', intval($request->paid));
		}
		$orders = $items->paginate(12);

		$this->result['data']['orders'] = $orders;
		$this->result['status_code'] = 200;

		return response()->json($this->result, $this->result['status_code']);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return json $data
	 */
	public function store(Request $request)
	{
		$rules = array(
			'customer_email' => 'required|exists:customers,email|unique:orders,customer,NULL,id,paid,0'
		);

		$validator = \Validator::make($request->all(), $rules);
		if (!$validator->fails()) {
			$input = [
				'customer' => $request->customer_email,
				'paid' => 0,
			];
			$order = Order::create($input);
			$this->result['data']['order'] = $order;
			$this->result['status_code'] = 200;
		} else {
			$msgArr = $validator->messages();
			$this->result["validation_errors"]  = $msgArr;
			$this->result['status_code'] = 400;
		}

		return response()->json($this->result, $this->result['status_code']);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$order = Order::with('products')->where('id', $id)->first();
		if ($order) {
			$this->result['status_code'] = 200;
			$this->result['data']['order'] = $order;
		} else {
			$this->result['message'] = "Invalid order id!";
		}

		return response()->json($this->result, $this->result['status_code']);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return json $data
	 */
	public function update(Request $request, $id)
	{
		$rules = array(
			'customer_email' => 'required|exists:customers,email|unique:orders,customer,' . $id . ',id,paid,0'
		);

		$validator = \Validator::make($request->all(), $rules);
		if (!$validator->fails()) {
			$input = [
				'customer' => $request->customer_email,
			];
			$order = Order::where('id', $id)->where('paid', 0)->first();
			if ($order) {
				$order->update($input);
				$this->result['data']['order'] = $order;
				$this->result['status_code'] = 200;
				$this->result['message'] = "Order detail updated!";
			} else {
				$this->result['message'] = "Invalid order id!";
			}
		} else {
			$msgArr = $validator->messages();
			$this->result["validation_errors"]  = $msgArr;
		}

		return response()->json($this->result, $this->result['status_code']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return json $data
	 */
	public function destroy($id)
	{
		$order = Order::where('id', $id)->first();
		if ($order) {
			if ($order->paid == 1) {
				$this->result['message'] = "This order has already been processed!";
			} else {
				$order->delete();
				$this->result['status_code'] = 200;
				$this->result['message'] = "Order deleted!";
			}
		} else {
			$this->result['message'] = "Invalid order id!";
		}

		return response()->json($this->result, $this->result['status_code']);
	}

	/**
	 * Add product to order.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return json $data
	 */
	public function addProduct(Request $request, $id)
	{
		$rules = array(
			'product_id' => 'required|exists:products,id'
		);

		$validator = \Validator::make($request->all(), $rules);
		if (!$validator->fails()) {
			$order = Order::where('id', $id)->where('paid', 0)->first();
			if ($order) {
				$quantity = $request->get('quantity', 1);
				$product = Product::where('id', $request->product_id)->first();
				$orderItem = OrderItem::where('order_id', $id)->where('product_id', $request->product_id)->first();
				if (!$orderItem) {
					$orderItem = new OrderItem();
					$orderItem->order_id = $id;
					$orderItem->product_id = $request->product_id;
				}
				$orderItem->quantity = ($request->has('quantity')) ? $quantity : ($orderItem->quantity + $quantity);
				$orderItem->price = $product->price;
				$orderItem->save();
				$order->updateCart();

				$this->result['data']['order'] = $order;
				$this->result['status_code'] = 200;
				$this->result['message'] = "Item added to order!";
			} else {
				$this->result['message'] = "Invalid order id!";
			}
		} else {
			$msgArr = $validator->messages();
			$this->result["validation_errors"]  = $msgArr;
		}

		return response()->json($this->result, $this->result['status_code']);
	}

	/**
	 * Remove the specified product from order.
	 *
	 * @param  int  $orderId
	 * @param  int  $productId
	 * @return json $data
	 */
	public function removeProduct($orderId, $productId)
	{
		$orderitem = OrderItem::where('order_id', $orderId)->where('product_id', $productId)->first();
		if ($orderitem && $orderitem->order) {
			if ($orderitem->order->paid == 1) {
				$this->result['message'] = "This order has already been processed!";
			} else {
				$order = $orderitem->order;
				$orderitem->delete();
				$order->updateCart();
				$this->result['data']['order'] = $order;
				$this->result['status_code'] = 200;
				$this->result['message'] = "Product removed!";
			}
		} else {
			$this->result['message'] = "Invalid order item!";
		}

		return response()->json($this->result, $this->result['status_code']);
	}

	/**
	 * Payment for specified order.
	 *
	 * @param  int  $id
	 * @return json $data
	 */
	public function pay($id)
	{
		$order = Order::where('id', $id)->first();
		if ($order) {
			$order->updateCart();
			if ($order->paid == 1) {
				$this->result['message'] = "This order has already been processed!";
			} else if ($order->payable_amount <= 0) {
				$this->result['message'] = "Please add the product to process an order!";
			} else {
				$payment = $this->pay->superPayment($order);
				if ($payment['status']) {
					$order->paid = 1;
					$order->paid_amount = $order->payable_amount;
					$order->save();
					$this->result['status_code'] = 200;
				} else {
					$this->result['status_code'] = 400;
				}
				$this->result['message'] = $payment['message'];
			}
		} else {
			$this->result['message'] = "Invalid order id!";
		}

		return response()->json($this->result, $this->result['status_code']);
	}
}
