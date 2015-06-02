/**
 * Controls the 'packages.view' page
 */
app.controller('ViewController', function($scope, $rootScope, $http, $state, $stateParams, $modal) {

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
	$scope.reset = function() {
		/* reset the data */
		$scope.setup();
	}

	// Indicate we are waiting for data
	$scope.request = { package: $stateParams.package };
	$rootScope.waiting = true;

	// Send the request to the PHP script
	$http.post("php/view.php", $scope.request)
	.success(function(data) {
		// process the response
		if (data["error"]) {
			$rootScope.error = "Error: " + data["error"];
		} else {
			$rootScope.details = data["data"];
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

