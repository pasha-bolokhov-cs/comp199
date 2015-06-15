/**
 * Controls the 'packages.view' page
 */
app.controller('PackagesViewController', function($scope, $rootScope, $http, $state, $stateParams, $modal) {

	/*
	 * Permanent initialization
	 */

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Data Initialization
		$scope.request = {};
		$scope.result = undefined;
	}
	$scope.setup();

	/*
	 * Functions assigned to buttons
	 */
	$scope.go = function() {
		/* check that a packages was passed as a parameter */
		if (!$stateParams.package) {
			$rootScope.error = "Cannot add a package to the cart - package has not been chosen";
			return;
		}

		/* switch to the 'Trips' page */
		goToTrips = function() {
			$state.go('user.trips', { package: $stateParams.package });
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


	// Check that a packages was passed as a parameter
	if (!$stateParams.package) {
		$rootScope.error = "Cannot retrieve package details - package has not been chosen";
		return;
	}
	$scope.request = { package: $stateParams.package };

	// Indicate we are waiting for data
	$rootScope.waiting = true;

	// Send the request to the PHP script
	$http.post("php/packages.view.php", $scope.request)
	.success(function(data) {
		// process the response
		if (data["error"]) {
			$rootScope.error = "Error: " + data["error"];
			return;
		}

		// total success
		if (!("package" in data) || !("segments" in data)) {
			$rootScope.error = "Error: incomplete response from server";
			return;
		}
		$scope.package = data.package;
		$scope.details = data.segments;
	})
	.error(function(data, status) {
		console.log(data);
		$rootScope.error = "Error accessing the server: " + status + ".";
	})
	.finally(function() { 
		// Indicate that we have an answer
		$rootScope.waiting = false;
	});

	// extract a heading from a segment
	$scope.getHeading = function(seg) {
		if (!seg)
			return null;

		if (seg.transport)
			return seg.transport + ":  " + seg.origin.city + " to " + seg.destination.city;

		if (seg.activity)
			return seg.activity.name;

		if (seg.hotel)
			return "Staying at " + seg.hotel;
	}
});
