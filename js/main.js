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
		// Packages
		.state("guest.packages", {
			url: "packages",
			views: {
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

	console.log("in PageController");
});
