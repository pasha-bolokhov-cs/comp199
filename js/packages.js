/**
 * Controls the 'packages' page
 */
// GG remove '$sce' if not needed
app.controller('PackagesController', function($scope, $rootScope, $http, $cookies, $sce) {

	/*
	 * Permanent initialization
	 */

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Initialization
		$scope.showError = false;
		$scope.error = false;
		$scope.waitingRegions = false;
		$scope.waitingPackages = false;

		// Data Initialization
		$scope.request = {};
		$scope.result = undefined;
	}
	$scope.setup();

	/*
	 * Functions assigned to buttons
	 */
	$scope.reset = function() {
		/* reset the data */
		$scope.setup();
	}
	$rootScope.regionSelect = function() {
		// Indicate we are waiting for data
		$scope.waitingPackages = true;
		$scope.request = { region: $rootScope.region.region };
	
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
			$scope.showError = true;
		});
	}


	/*
	 * Get the list of regions
	 */
	$rootScope.showRegions = false;
	$scope.waitingRegions = true;
	$rootScope.region = {"region": "All", "available": true};
	$http.post("php/get-regions.php")
	.success(function(data) {
		// process the response
		if (data["error"]) {
			$scope.error = "Error: " + data["error"];
		} else {
			// form a new list of regions with "All" prepended
			$rootScope.regions = [ $rootScope.region ];
			for (var r in data["regions"]) {
				$rootScope.regions.push({
					"region": data["regions"][r].region,
					"available": data["regions"][r].available
				});
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
	$rootScope.regionSelect();
});
