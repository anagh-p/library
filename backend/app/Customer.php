<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    public function getAllCustomers($post) {
        $books = Customer::select("customer_id", "customer_name")
                ->where("customer_name", "like", "%" . $post['search_customer'] . "%")
                ->orWhere("customer_email", "like", "%" . $post['search_customer'] . "%")
                ->orWhere("customer_phone", "like", "%" . $post['search_customer'] . "%")
                ->orderBy("customer_name", "ASC")
                ->get()
                ->toArray();
        return $books;
    }

}
