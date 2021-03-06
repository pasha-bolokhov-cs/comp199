/**
 * Main AngularJS Application
 */
var app = angular.module('albatrossApp', ['ui.router', 'ui.bootstrap', 'ngMessages', 
					  'ngSanitize', 'angular-jwt', 'ngStorage']);

/**
 *	'responseInterceptor' catches JWT tokens in any HTTP response
 */
app.factory('responseInterceptor', function($rootScope, jwtHelper) {
	checkResponse = function(response) {
		if (response && response.data && typeof response.data === 'object') {
			/* check for the presence of a token */
			if (response.data.jwt) {
				// save the received token
				$rootScope.storage.jwt = response.data.jwt;
				$rootScope.storage.token = jwtHelper.decodeToken(response.data.jwt);
			}

			/* delete the token in case of authentication error */
			if (response.data.error && response.data.error === "authentication" &&
			    $rootScope.storage) {
				if ($rootScope.storage.token)
					delete $rootScope.storage.token;
				if ($rootScope.storage.jwt)
					delete $rootScope.storage.jwt;
			}
		}
		return response;
	};
	return {
		response: checkResponse	// success
					// do not assume to receive a JWT in case of an error
	};
});

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
				"brand": {	// The brand name
					templateUrl: "partials/guest/brand.html"
				},
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
				"brand": {	// The brand name
					templateUrl: "partials/user/brand.html"
				},
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
					controller: "HomeController"
				}
			}
		})
		// Packages - shared (duplicated) between "guest" and "user"
		.state("guest.packagesRoot", {	// This parent state just calls the controller
			url: "/packages",
			abstract: true
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
		.state("guest.packagesRoot.packages.view", {
			url: "/view",
			params: {
				package: null
			},
			views: {
				"select-region-view@": {	// The view in the root state
					// Do not show the region select tool
				},
				"@": {		// Targets the unnamed view in the root state
					templateUrl: "partials/packages.view.html",
					controller: "PackagesViewController"
				}
			}
		})
		/*
		 * User states
		 */
		// Home
		.state("user.home", {
			url: "/home",
			views: {
				"@": {		// Targets the unnamed view in the root state
					templateUrl: "partials/home.html",
					controller: "HomeController"
				}
			}
		})
		.state("user.packagesRoot", { // This parent state just calls the controller
			url: "/packages",
			abstract: true
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
		})
		.state("user.packagesRoot.packages.view", {
			url: "/view",
			params: {
				package: null
			},
			views: {
				"select-region-view@": {	// The view in the root state
					// Do not show the region select tool
				},
				"@": {		// Targets the unnamed view in the root state
					templateUrl: "partials/packages.view.html",
					controller: "PackagesViewController"
				}
			}
		})
		.state("user.profile",{
			url: "/profile",
			views: {
				"@": {
					templateUrl: "partials/user/profile.html",
					controller: "ProfileController"
				}
			}
		})
		.state("user.profile.view", {
			url: "/view",
			views: {
				"controls@user.profile": {
					templateUrl: "partials/user/profile.view.html",
					controller: "ProfileViewController"
				}
			}			
		})
		.state("user.profile.modify", {
                        url: "/modify",
			views: {
				"controls@user.profile": {
					templateUrl: "partials/user/profile.modify.html",
					controller: "ProfileModifyController"
				}
			}	
		})
		.state("user.profile.password", {
			url: "/password",
			views: {
				"password@user.profile": {
					templateUrl: "partials/user/profile.password-form.html",
					controller: "ProfilePasswordFormController"
				},
				"controls@user.profile": {
					templateUrl: "partials/user/profile.password-controls.html",
					controller: "ProfilePasswordSubmitController"
				}
			}	
		})
		.state("user.trips", {
			url: "/trips",
			params: {
				package: null
			},
			views: {
				"@": {
					templateUrl: "partials/user/trips.html",
					controller: "TripsController"
				}
			}
		});
	$urlRouterProvider.otherwise("/guest/home");

	/*
	 * Configure JWT
	 */
	jwtInterceptorProvider.tokenGetter = ['config', '$rootScope', function(config, $rootScope) {
		// Only apply authentication to secure PHP script requests
		if ($rootScope.storage && $rootScope.storage.jwt &&
		    config.url.match(/php\/secure\/.+\.php$/))
			return $rootScope.storage.jwt;

		return null;
	}];
	$httpProvider.interceptors.push('jwtInterceptor');
	$httpProvider.interceptors.push('responseInterceptor');		// catch tokens in the response
});


/**
 * Controls the app
 */
app.controller('MainController', function ($scope, $rootScope, $q, $http, $modal, $state, $stateParams,
					   jwtHelper, $localStorage /* also: $location */) {

	/*
	 * Permanent initialization
	 */
	/* Pattern used for quick client-side string validation */
	$rootScope.stringPattern = /^([a-z]|[0-9]|[\+\-\@.]|\s)*$/i;

	/* Arrange the page for the sign-in */
	$rootScope.doSignIn = function() {
		/* no action if already in user-space */
		if ($state.includes("user"))
			return;

		/* convert the current state to a user-space state */
		var newState = $state.current.name.replace(/^guest\./, "user.");

		/* check if it exists and load a default state if not */
		if ($state.get(newState))
			$state.go(newState, $stateParams);
		else
			$state.go("user.home", $stateParams);
	};

	/* Arrange the page for the sign-out */
	$rootScope.doSignOut = function() {
		/* clear the token */
		delete $rootScope.storage.token;
		delete $rootScope.storage.jwt;

		/* no more action if already in guest-space */
		if ($state.includes("guest"))
			return;

		/* convert the current state to a user-space state */
		var newState = $state.current.name.replace(/^user\./, "guest.");

		/* check if it exists and load a default state if not */
		if ($state.get(newState))
			$state.go(newState, $stateParams);
		else
			$state.go("guest.home", $stateParams);
	};

	/* Make a reference to $localStorage */
	$rootScope.storage = $localStorage;

	/* Mild authentication - just test the token */
	$rootScope.mildAuthenticate = function() {
		return $q(function(resolve, reject) {
			$http.post("php/secure/check-token.php")
			.success(function(data) {
				// process the response
				if (data["error"]) {
					switch (data["error"]) {
					case "authentication":
						// silently quit
						break;

					default:
						$rootScope.error = "Error: " + data["error"];
						console.log("other error during authentication -", data["error"]);
					}
					reject(data["error"]);
					return;
				}
				// total success
				resolve();
				return;
			})
			.error(function(data, status) {
				console.log(data);
				$rootScope.error = "Error accessing the server: " + status + ".";
				reject(data["error"]);
			})
			.finally(function() {
			});
		});
	};


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
		/* on successful authentication - change the state appropriately */
		modal.result.then($rootScope.doSignIn);
	};

	/* 'Sign Out' in the navigation bar */
	$rootScope.signOut = function() {
		$rootScope.doSignOut();
	};


	/*
	 * Perform "mild" authentication if a token is found
	 */
	if ($rootScope.storage.jwt) {
		$rootScope.mildAuthenticate().then(
			$rootScope.doSignIn,		// change the state to 'signed in'
			$rootScope.doSignOut		// clear the token and change the state if necessary
		);
	}
});
