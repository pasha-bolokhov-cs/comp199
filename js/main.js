/**
 * Main AngularJS Application
 */
var app = angular.module('albatrossApp', ['ui.router', 'ui.bootstrap', 'ngMessages', 
					  'ngCookies', 'ngSanitize', 'angular-jwt']);


/**
 * Configure the Routes
 */
app.config(['$routeProvider', function ($routeProvider) {
	$routeProvider
		// Home
		.when("/",		{templateUrl: "partials/home.html", controller: "PageController"})
		.when("/home",		{templateUrl: "partials/home.html", controller: "PageController"})
		// Pages
		.when("/packages",	{templateUrl: "partials/packages.html", controller: "PackagesController"})
		.when("/trips",		{templateUrl: "partials/orders.html", controller: "OrdersController"})
		.when("/profile",	{templateUrl: "partials/profile.html", controller: "PageController"})
		// else 404
		.otherwise("/404",	{templateUrl: "partials/404.html", controller: "PageController"});
}]);


/**
 * Controls the app
 */
app.controller('MainController', function ($scope, $rootScope, $modal /* also: $location, $http */) {

	/*
	 * Permanent initialization
	 */
	/* GG Arrange the page for the sign-out */
	$rootScope.doSignOut = function() {
		$rootScope.signedIn = false;
	}

	/*
	 * Resettable data initialization
	 */
	$rootScope.signedIn = false;

	/*
	 * Functions assigned to buttons
	 */
	/* 'Sign Up' in the navigation bar */
	$rootScope.signUp = function() {
		var modal = $modal.open({
			animation: true,			// whether to use animation
			templateUrl: 'partials/signup.html',	// what to show in the modal
			size: 'md',				// size
			backdrop: 'static',			// clicking outside does not close the window
			controller: 'SignUpController'		// the controller of the opened page
		});
	};

	/* 'Sign In' in the navigation bar */
	$rootScope.signIn = function() {
		var modal = $modal.open({
			animation: true,			// whether to use animation
			templateUrl: 'partials/signin.html',	// what to show in the modal
			size: 'sm',				// size
			backdrop: 'static',			// clicking outside does not close the window
			controller: 'SignInController'		// the controller of the opened page
		});
	}

	/* 'Sign Out' in the navigation bar */
	$rootScope.signOut = function() {
		// GG send a "log-out" notification to the server

		$rootScope.doSignOut();
	}
});


/**
 * Controls other pages
 */
app.controller('PageController', function ($scope, $rootScope, $modal /* also: $location, $http */) {

	$rootScope.onPackagesPage = false;

});
