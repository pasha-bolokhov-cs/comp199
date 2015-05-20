/**
 * Controls the 'packages' page
 */
// GG remove '$sce' if not needed
app.controller('PackagesController', function($scope, $rootScope, $http, $state, $modal, $sce) {

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
	$rootScope.regionSelect = function(r) {
		// Switch the current region to the selected value
		$rootScope.region = r;

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
	};
	$scope.go = function(name) {
		/* switch to the 'Trips' page */
		goToTrips = function() {
console.log("switching to the 'trips' page with package `" + name + "'"); //GG
			$state.go('user.trips', { package: name });
		}
		/* offer a login modal if not in user space */
		if (!$state.current.name.match(/^user\./)) {
			var modal = $modal.open({
				animation: true,			// whether to use animation
				templateUrl: 'partials/signin.html',	// what to show in the modal
				size: 'sm',				// size
				backdrop: 'static',			// clicking outside does not close the window
				controller: 'SignInController'		// the controller of the opened page
			});
			/* on successful authentication - change the state to "trips" */
			modal.result.then(goToTrips);
			return;
		}
		goToTrips();
	};

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
			// get the packages
			$rootScope.regionSelect($rootScope.region);
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
});
