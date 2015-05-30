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
		// Initialization
		$scope.showError = false;
		$scope.error = false;
		$scope.waiting = false;

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

	// Indicate we are waiting for data
	$scope.request = { package: $stateParams.package };
	$scope.waiting = true;

	// Send the request to the PHP script
	$http.post("php/packages.view.php", $scope.request)
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
		$scope.showError = true;
	});

	$scope.go = function() {
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

});
