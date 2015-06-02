/**
 * Controls the 'packages' page
 */
app.controller('PackagesController', function($scope, $rootScope, $http, $state) {

	/*
	 * Permanent initialization
	 */

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Data Initialization
		$scope.request = {};
		$scope.packages = undefined;
	}
	$scope.setup();

	/*
	 * Functions assigned to buttons
	 */
	$scope.reset = function() {
		/* reset the data */
		$scope.setup();
	}
	$rootScope.regionSelect = function(r) {
		// Switch the current region to the selected value
		$rootScope.region = r;

		// Indicate we are waiting for data
		$rootScope.waiting = true;
		$scope.request = { region: $rootScope.region.region };
	
		// Send the request to the PHP script
		$http.post("php/packages.php", $scope.request)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				$rootScope.error = "Error: " + data["error"];
			} else {
				$scope.packages = data["data"];
			}
		})
		.error(function(data, status) {
			console.log(data);
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;
		});
	};
	$scope.view = function(name) {
		$state.go('^.view', { package: name });
	};

	/*
	 * Get the list of regions
	 */
	$rootScope.showRegions = false;
	$rootScope.region = {"region": "All", "available": true};
	$http.post("php/get-regions.php")
	.success(function(data) {
		// process the response
		if (data["error"]) {
			$rootScope.error = "Error: " + data["error"];
		} else {
			// form a new list of regions with "All" prepended
			$rootScope.regions = [ $rootScope.region ];
			for (var r in data["regions"]) {
				$rootScope.regions.push({
					"region": data["regions"][r].region,
					"available": data["regions"][r].available
				});
			}
			// get the packages
			$rootScope.regionSelect($rootScope.region);
			$rootScope.showRegions = true;
		}
	})
	.error(function(data, status) {
		console.log(data);
		$rootScope.error = "Error accessing the server: " + status + ".";
	})
	.finally(function() { 
		$scope.waiting = false;
	});
});
