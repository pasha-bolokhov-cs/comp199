/**
 * Main AngularJS Application
 */
var app = angular.module('albatrossApp', ['ui.router', 'ui.bootstrap', 'ngMessages', 
					  'ngCookies', 'ngSanitize', 'angular-jwt']);


/**
 * Configure the Routes
 */
app.config(function($stateProvider, $urlRouterProvider) {
	$stateProvider
		// "Guest" state - not logged in as a user
		.state("guest", {
			abstract: true,
			url: "/",
			views: {
				"navigation-left-view": {
					templateUrl: "partials/guest/navigation-left.html"
				},
				"navigation-right-view": {
					templateUrl: "partials/guest/navigation-right.html"
				}
			}
		})
		// "User" state - logged in as a user
		.state("user", {
			abstract: true,
			url: "/user",
			views: {
				"navigation-left-view": {
					templateUrl: "partials/user/navigation-left.html"
				},
				"navigation-right-view": {
					templateUrl: "partials/user/navigation-right.html"
				}
			}
		})
		/*
		 * Guest states
		 */
		// Home
		.state("guest.home", {
			url: "",		// Does not add anything to the parent URL
			views: {
				"@": {		// Targets the unnamed view in the root state
					templateUrl: "partials/home.html",
					controller: "PageController"
				}
			}
		})
		// Packages - shared (duplicated) between "guest" and "user"
		.state("guest.packagesRoot", {	// This parent state just calls the controller
			url: "/packages",
			abstract: true,
			controller: "PackagesController"
		})
		.state("guest.packagesRoot.packages", {
			url: "",
			views: {
				"select-region-view@": {	// The view in the root state
					templateUrl: "partials/regions.html"
				},
				"@": {		// Targets the unnamed view in the root state
					templateUrl: "partials/packages.html",
					controller: "PackagesController"
				}
			}
		})
		/*
		 * User states
		 */
		.state("user.packagesRoot", { // This parent state just calls the controller
			url: "/packages",
			abstract: true,
			controller: "PackagesController"
		})
		.state("user.packagesRoot.packages", {
			url: "",
			views: {
				"select-region-view@": {	// The view in the root state
					templateUrl: "partials/regions.html"
				},
				"@": {		// Targets the unnamed view in the root state
					templateUrl: "partials/packages.html",
					controller: "PackagesController"
				}
			}
		});
	$urlRouterProvider.otherwise("/");
});


/**
 * Controls the app
 */
app.controller('MainController', function ($scope, $rootScope, $modal /* also: $location, $http */) {

	/*
	 * Permanent initialization
	 */
	/* GG Arrange the page for the sign-out */
	$rootScope.doSignOut = function() {
	}

	/*
	 * Resettable data initialization
	 */


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
});
