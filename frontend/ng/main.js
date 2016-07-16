var app = angular.module('library', [
    'ngRoute',
    'ngCookies'
]);

app.config(function ($routeProvider) {
    $routeProvider.
            when('/login', {
                templateUrl: 'frontend/shared/login.html',
                controller: 'LoginController'
            }).
            when('/books', {
                templateUrl: 'frontend/components/books/views/list.html'
            }).
            when('/history', {
                templateUrl: 'frontend/components/users/views/history.html',
            }).
            otherwise({
                redirectTo: '/books'
            });
});
