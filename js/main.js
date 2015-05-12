/**
 * Main AngularJS Application
 */
var app = angular.module('albatrossApp', ['ui.router', 'ui.bootstrap', 'ngMessages', 
					  'ngCookies', 'ngSanitize', 'angular-jwt']);


/**
 * Configure the Routes
 */
app.config(['$routeProvider', 'urlRouterProvider', function ($stateProvider, $urlRouterProvider) {
	$stateProvider
		.state("guest", {
			url: "",
			views: {
				"navigation-left-view": { template: "guest-navigation-left-view.html" },
				"navigation-right-view": { template: "guest-navigation-right-view.html" },
			}
		})
		.state("user", {
			url: "",
			views: {
				"navigation-left-view": { template: "user-navigation-left-view.html" },
				"navigation-right-view": { template: "user-navigation-right-view.html" },
			}
		})
		// Home
		.state("guest.home", {url: "/home", templateUrl: "partials/home.html", controller: "PageController"})
		// Pages
		.state("guest.packages", {url: "/packages", templateUrl: "partials/packages.html", controller: "PackagesController"});
	$urlRouterProvider.otherwise("guest.home");
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
