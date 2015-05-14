/**
 * Main AngularJS Application
 */
var app = angular.module('albatrossApp', ['ui.router', 'ui.bootstrap', 'ngMessages', 
					  'ngSanitize', 'angular-jwt']);


app.config(function($stateProvider, $urlRouterProvider, $httpProvider, jwtInterceptorProvider) {
	/*
	 * Configure the routes
	 */
	$stateProvider
		// "Guest" state - not logged in as a user
		.state("guest", {
			abstract: true,
			url: "/guest",
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
			url: "/home",
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
	$urlRouterProvider.otherwise("/guest/home");

	/*
	 * Configure JWT
	 */
	jwtInterceptorProvider.tokenGetter = ['config', function(config) {
		// Do not apply authentication to anything in "partials" directory
		if (config.url.match(/partials\//))
			return null;

//GG		return $localStorage.token;
	}];
	$httpProvider.interceptors.push('jwtInterceptor');
});


/**
 * Controls the app
 */
app.controller('MainController', function ($scope, $rootScope, $modal, $state /* also: $location, $http */) {

	/*
	 * Permanent initialization
	 */
	/* Arrange the page for the sign-in */
	$rootScope.doSignIn = function(name) {
		/* show the user's name */
		$rootScope.loginName = name;

		/* convert the current state to a user-space state */
		var newState = $state.current.name.replace(/^guest\./, "user.");

		/* check if it exists and load a default state if not */
		if ($state.get(newState))
			$state.go(newState);
		else
			$state.go("user.packagesRoot.packages");
	}


	/* Arrange the page for the sign-out */
	$rootScope.doSignOut = function() {
		/* convert the current state to a user-space state */
		var newState = $state.current.name.replace(/^user\./, "guest.");

		/* check if it exists and load a default state if not */
		if ($state.get(newState))
			$state.go(newState);
		else
			$state.go("guest.home");
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
