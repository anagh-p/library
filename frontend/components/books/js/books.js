app.controller('BookController', function ($scope, $http, $cookies, $location) {

    //Client side checking to whether a valid user is logged in.
    if (angular.isUndefined($cookies.get("login_string")) || angular.isUndefined($cookies.get("user_id"))) {
        $location.path("/login");
        return;
    } else {
        if ($cookies.get("login_string").length < 60) {
            $location.path("/login");
            return;
        }
    }


    $scope.search_book = '';//Model to recieve the search keyword.
    $scope.book_searched = false;//Variable set to check if a search has been made.

    /**
     * Function called to fetch all the details of the books.
     * @returns {undefined}
     */
    var getAllBooks = function () {

        var details = {
            'user_id': $cookies.get("user_id"),
            'login_string': $cookies.get("login_string"),
            'search_book': $scope.search_book
        }
        var data = $.param(details)
        var url = '/library/backend/public/library/getAllBooks';
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        }

        //Make the server call.
        $http.post(url, data, config)
                .success(function (data, status) {
                    $scope.books = data;
                    $scope.book_searched = false;
                })
    }
    getAllBooks(); //Call the function to list the books.

    //Call the function when a book is being searched.
    $scope.$watch('search_book', function () {
        if ($scope.book_searched) {
            getAllBooks();
        }
    })


    /**
     * Function used to prepare the data for lending the book to a customer.
     * @param {integer} book_index The index position of the book in the array.
     * @returns {undefined}
     */
    $scope.prepareLendData = function (book_index) {
        getAllCustomers();//List the customers right when the click is made.
        $scope.search_customer = '';//Clear any previous search.
        $scope.customers = [];//Clear the previously searched results.

        $scope.lendBookDetails = {
            lend_book_id: $scope.books[book_index].book_id,
            lend_book_title: $scope.books[book_index].book_title,
            lend_book_author: $scope.books[book_index].book_author
        }
    }

    $scope.search_customer = '';//Value of the search being made.
    $scope.customer_searched = false;//No customer search is made until something is typed in the input.
    $scope.customers = [];//Array to store the customer list.

    /**
     * Function to fetch the list of customers.
     * @returns {undefined}
     */
    var getAllCustomers = function () {

        var details = {
            'user_id': $cookies.get("user_id"),
            'login_string': $cookies.get("login_string"),
            'search_customer': $scope.search_customer
        }

        var data = $.param(details)
        var url = '/library/backend/public/customers/getAllCustomers';
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        }

        //Make the server call.
        $http.post(url, data, config)
                .success(function (data, status) {
                    $scope.customers = data;
                    $scope.customer_searched = false;
                })
    }

    //Call the function when a customer is being searched.
    $scope.$watch('search_customer', function () {
        if ($scope.customer_searched) {
            getAllCustomers();
        }
    });

    /**
     * Function called when a customer is selected from the dropdown list.
     * @param {array} customer
     * @returns {undefined}
     */
    $scope.selectCustomer = function (customer) {
        $scope.search_customer = customer.customer_name;//Set value in the search box.
        selectedCustomerId = customer.customer_id;//Store the ID of the selected customer in a variable.
        $scope.customers = [];
    }


    $scope.alreadyRented = false;//Flag to notify if the book is already rented.
    $scope.rentSuccessful = false;//Flag to notify if the lending is successful.

    /**
     * Function to lend a book.
     * @returns {undefined}
     */
    $scope.lend = function () {
        var details = {
            'user_id': $cookies.get("user_id"),
            'login_string': $cookies.get("login_string"),
            'book_id': $scope.lendBookDetails.lend_book_id,
            'customer_id': selectedCustomerId, //Variable stored on selecting a customer from the dropdown list..
        }
        var data = $.param(details)
        var url = '/library/backend/public/library/lend';
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        }

        //Make the server call.
        $http.post(url, data, config)
                .success(function (data, status) {
                    if (data == -1) {//The book is already rented by this customer.
                        $scope.alreadyRented = true;
                    } else if (data == 1) {//Successful lending
                        angular.element("#lend_book").modal("hide");
                        getAllBooks();
                        $scope.rentSuccessful = true;
                    } else {//Unexpected error.
                        console.log("Something happened.")
                    }
                })
    }

});