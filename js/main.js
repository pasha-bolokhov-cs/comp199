/**
 * Main AngularJS Application
 */
var app = angular.module('albatrossApp', ['ui.router', 'ui.bootstrap', 'ngSanitize']);


/**
 * Configure the Routes
 */
app.config(['$routeProvider', function ($routeProvider) {
	$routeProvider
		// Home
		.when("/",		{templateUrl: "partials/home.html", controller: "PageController"})
		.when("/home",		{templateUrl: "partials/home.html", controller: "PageController"})
		// Pages
		.when("/packages",	{templateUrl: "partials/packages.html", controller: "PageController"})
		.when("/trips",		{templateUrl: "partials/trips.html", controller: "PageController"})
		.when("/profile",	{templateUrl: "partials/profile.html", controller: "PageController"})
		.when("/signup",	{templateUrl: "partials/signup.html", controller: "PageController"})
		// else 404
		.otherwise("/404",	{templateUrl: "partials/404.html", controller: "PageController"});
}]);


/**
 * Controls all other Pages
 */
app.controller('PageController', function ($scope, $modal /* also: $location, $http */) {
});
