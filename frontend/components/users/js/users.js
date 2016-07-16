app.controller('LoginController', function ($scope, $http, $location, $cookies) {
    
    $scope.user = {
        'email': '',
        'password': ''
    }

    $scope.loginFail = false;
    
    /**
     * Attempt to login the user with the credentials entered.
     * @returns {undefined}
     */
    $scope.login = function () {

        var details = {
            'email': $scope.user.email,
            'password': $scope.user.password
        }

        var url = '/library/backend/public/login';
        var data = $.param(details)
        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        }
        
        $http.post(url, data, config)
                .then(
                        function (response, status) {
                            if (response.data.userId > 0) {//If sucessful login
                                $cookies.put("user_id", response.data.userId);
                                $cookies.put("login_string", response.data.randomId);
                                $location.path("/books");
                            } else {//If login fail.
                                $cookies.put("authenticated", "0");
                                $scope.loginFail = true;
                            }
                        },
                        function (response) {
                            //Do something in case of an error.
                        }
                );
    }
});