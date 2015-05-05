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
});


/**
 * Controls the 'packages' page
 */
app.controller('PackagesController', function($scope, $rootScope, $http, $sce) {

	// Permanent initialization
	$rootScope.onPackagesPage = true;
	$rootScope.regionSelect = function() {
		console.log("Region changed to " + $rootScope.region); // GG
	}

	// Resettable data initialization
	$scope.setup = function() {
		// Initialization
		$scope.showResult = false;
		$scope.error = false;
		$scope.waiting = false;

		// Data Initialization
		$scope.request = {};
		$scope.result = undefined;
	}
	$scope.setup();

	// Function assigned to a button
	$scope.reset = function() {
	    /* reset the data */
	    $scope.setup();
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
			$rootScope.regions = data["regions"];
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

	/* Request object is ready to send */
	$scope.waiting = true;

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
		$scope.waiting = false;
		$scope.showResult = true;
	});
});
