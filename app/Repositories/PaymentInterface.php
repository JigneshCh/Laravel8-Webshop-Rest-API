<?php

namespace App\Repositories;

interface PaymentInterface
{

    /**
     * Process payement
     * 
     * @param $order array 
     */
    public function superPayment($order);
}
