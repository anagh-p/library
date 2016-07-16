<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Book;

class BookController extends Controller {

    var $book;

    public function __construct() {
        $this->book = new Book();
    }

    /**
     * Fetch the deatils of all the books that are in the library.
     * @param Request $request
     * @return array
     */
    public function getAllBooks(Request $request) {
        $params = $request->all();
        $books = $this->book->getAllBooks($params);
        return $books;
    }

    /**
     * Function to lend the book to the customer.
     * @param Request $request
     * @return type
     */
    public function lend(Request $request) {
        $params = $request->all();

        //First check if the book is already rented by the customer.
        $alreadyRented = $this->book->checkIfAlreadyRented($params);

        if (!$alreadyRented) {//If it's not rented,
            //Get the ID of the available copy that can be rented.
            $copyId = $this->book->getCopyId($params['book_id']);

            //Update the corresponding details in respective tables..
            $lendStaus = $this->book->lendBook($copyId, $params);

            return $lendStaus; //Will hold 0 or 1 to identify if the rent operation was successful or not.
        } else {//If it's already rented, do not allow the operation.
            return -1; //Used to identify that the renting failed because the book is already rented by the customer.
        }
    }

}
