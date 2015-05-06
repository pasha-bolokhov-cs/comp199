/**
 * Main AngularJS Application
 */
var app = angular.module('albatrossApp', ['ngRoute', 'ui.bootstrap', 'ngSanitize']);


/**
 * Configure the Routes
 */
app.config(['$routeProvider', function ($routeProvider) {
	$routeProvider
		// Home
		.when("/",		{templateUrl: "partials/home.html", controller: "MainController"})
		.when("/home",		{templateUrl: "partials/home.html", controller: "MainController"})
		// Pages
		.when("/packages",	{templateUrl: "partials/packages.html", controller: "PackagesController"})
		.when("/trips",		{templateUrl: "partials/trips.html", controller: "MainController"})
		.when("/profile",	{templateUrl: "partials/profile.html", controller: "MainController"})
		.when("/signup",	{templateUrl: "partials/signup.html", controller: "MainController"})
		.when("/signin",	{templateUrl: "partials/signin.html", controller: "MainController"})
		// else 404
		.otherwise("/404",	{templateUrl: "partials/404.html", controller: "MainController"});
}]);


/**
 * Controls most other pages
 */
app.controller('MainController', function ($scope, $rootScope, $modal /* also: $location, $http */) {
	$rootScope.onPackagesPage = false;

	$rootScope.signUp = function() {
		var modal = $modal.open({
			animation: true,			// whether to use animation
			templateUrl: 'partials/signup.html',	// what to show in the modal
			size: 'md',				// size
			backdrop: 'static',			// clicking outside does not close the window
			controller: 'signUpController'		// the controller of the opened page
		});
	};
});


/**
 * Controls the 'Sign Up' modal
 */
app.controller('signUpController', function($scope, $rootScope, $modalInstance, $http) {

	/* 'Sign-up' button in the modal */
	$scope.signUp = function() {
		// GG
		$modalInstance.close();
	}

	/* 'Clear' button in the modal */
	$scope.clear = function() {
		// GG
	}

	/* 'Cancel' button in the modal */
	$scope.cancel = function() {
		$modalInstance.dismiss();
	}
});


/**
 * Controls the 'packages' page
 */
app.controller('PackagesController', function($scope, $rootScope, $http, $sce) {

	// Permanent initialization
	$rootScope.onPackagesPage = true;

	// Resettable data initialization
	$scope.setup = function() {
		// Initialization
		$scope.showResult = false;
		$scope.error = false;
		$scope.waitingRegions = false;
		$scope.waitingPackages = false;

		// Data Initialization
		$scope.request = {};
		$scope.result = undefined;
	}
	$scope.setup();

	// Functions assigned to buttons
	$scope.reset = function() {
	    /* reset the data */
	    $scope.setup();
	}
	$rootScope.regionSelect = function() {
		// Indicate we are waiting for data
		$scope.waitingPackages = true;
		$scope.request = { region: $rootScope.region };
	
		// Send the request to the PHP script
		$http.post("php/packages.php", $scope.request)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				$scope.error = "Error: " + data["error"];
			} else {
				$scope.result = data["data"];
			}
		})
		.error(function(data, status) {
			console.log(data);
			$scope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$scope.waitingPackages = false;
			$scope.showResult = true;
		});
	}


	/*
	 * Get the list of regions
	 */
	$rootScope.region = "All";
	$rootScope.showRegions = false;
	$scope.waitingRegions = true;
	$http.post("php/get_regions.php")
	.success(function(data) {
		// process the response
		if (data["error"]) {
			$scope.error = "Error: " + data["error"];
		} else {
			// form a new list of regions with "All" prepended
			$rootScope.regions = [ $rootScope.region ];
			for (var r in data["regions"]) {
				$rootScope.regions.push(data["regions"][r].region);
			}
		}
	})
	.error(function(data, status) {
		console.log(data);
		$scope.error = "Error accessing the server: " + status + ".";
	})
	.finally(function() { 
		$scope.waitingRegions = false;
		$rootScope.showRegions = true;
	});


	/*
	 * Get the packages
	 */
	$rootScope.regionSelect($rootScope.region);
});
