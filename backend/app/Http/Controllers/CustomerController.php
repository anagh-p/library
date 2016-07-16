<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Customer;

class CustomerController extends Controller {

    var $customer;

    public function __construct() {
        $this->customer = new Customer();
    }

    public function getAllCustomers(Request $request) {
        $params = $request->all();
        $customers = $this->customer->getAllCustomers($params);
        return $customers;
    }

}
