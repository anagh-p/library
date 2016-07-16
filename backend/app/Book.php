<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Book extends Model {

    protected $table = 'books';
    protected $primaryKey = 'book_id';
    public $timestamps = false;

    /**
     * Function to fetch the necessary details of all books.
     * @param array $post
     * @return array
     */
    public function getAllBooks($post) {
        $books = Book::select("book_id", "book_title", "book_author", "book_category", "category_title", "book_available")
                ->join("categories", "category_id", "=", "book_category")
                ->where("book_title", "like", "%" . $post['search_book'] . "%")
                ->orWhere("book_author", "like", "%" . $post['search_book'] . "%")
                ->orWhere("category_title", "like", "%" . $post['search_book'] . "%")
                ->orderBy("book_title", "ASC")
                ->get()
                ->toArray();
        return $books;
    }
    
    /**
     * Function to check if the book is currently available.
     * @param integer $bookId
     * @return string
     */
    public function checkAvailability($bookId) {
        $availability = DB::table("copies")
                ->where("copy_in_stock", "=", "1")
                ->where("copy_book_id", "=", $bookId)
                ->count();
        if ($availability == 0) {
            return "Unavailable";
        } else {
            return "Available";
        }
    }

    /**
     * Check if this book is already rented by the customer.
     * @param array $post
     * @return integer It will either be 0 or 1, but not boolean per se.
     */
    public function checkIfAlreadyRented($post) {
        $rented = DB::table("track")
                ->where("track_customer_id", "=", $post['customer_id'])
                ->where("track_book_id", "=", $post['book_id'])
                ->where("track_returned", "=", 0)//Already rented.
                ->count();

        return $rented;
    }

    /**
     * Function to get the ID of the first copy that can be rented out.
     * @param integer $bookId Book ID of which the copy ID has to be fetched.
     * @return integer ID of the copy that is available to be rented.
     */
    public function getCopyId($bookId) {
        $copyId = DB::table("copies")
                ->select("copy_id")
                ->where("copy_in_stock", "=", "1")
                ->where("copy_book_id", "=", $bookId)
                ->where("copy_status", "=", "available")
                ->orderBy("copy_id", "ASC")
                ->first();

        return $copyId->copy_id;
    }

    /**
     * Function to update the details when a customer is renting a book.
     * @param integer $copyId The ID of the copy that will be given to the customer.
     * @param array $post 
     * @return boolean Stating whether or not the operation was successul.
     */
    public function lendBook($copyId, $post) {

        $success = DB::transaction(function() use ($copyId, $post) {

                    //Insert the rent details to the table that tracks it.
                    $insertStatus = DB::table("track")
                            ->insert([
                        'track_copy_id' => $copyId,
                        'track_book_id' => $post['book_id'],
                        'track_customer_id' => $post['customer_id'],
                        'track_out_date' => date("Y-m-d H:i:s")
                    ]);

                    //Make this copy unavailable till returned by the customer.
                    $updateCopies = DB::table("copies")
                            ->where("copy_id", "=", $copyId)
                            ->update(["copy_in_stock" => 0]);

                    //Decrement the total copies available by 1.
                    $updateBooks = Book::where("book_id", "=", $post['book_id'])
                            ->decrement('book_available');

                    //If all of the above operations are successful.
                    if ($insertStatus && $updateCopies && $updateBooks) {
                        return 1;
                    } else {
                        return 0;
                    }
                });

        return $success;
    }

}
