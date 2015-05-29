/**
 * Controls the 'trips' page
 */
app.controller('TripsController', function($scope, $rootScope, $http, $state, $stateParams) {
	
	/* form the request if package name was supplied */
	$scope.request = {};
	if ($stateParams.package) {
		$scope.request.package = $stateParams.package;
	}
	
	$rootScope.waiting = true;
	$http.post("php/secure/retrieve-orders.php", $scope.request)
	.success(function(data) {
		// process the response
		if (data["error"]) {
			if (data["error"] == "authentication")
				$rootScope.doSignOut();
			else
				$scope.error = "Error: " + data["error"];
		} else {
			$scope.trips = data["data"];
		}
	})
	.error(function(data, status) {
		console.log(data);
		$rootScope.error = "Error accessing the server: " + status + ".";
	})
	.finally(function() { 
		$rootScope.waiting = false;
	});	



	$scope.orders = {};
	/* remove the orders */
	$scope.removeOrders = function(package) {
		$rootScope.waiting = true;
		$scope.orders.package = package;
		
		$http.post("php/secure/remove-order.php", $scope.orders)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				if (data["error"] == "authentication")
					$rootScope.doSignOut();
				else
					$scope.error = "Error: " + data["error"];
			} else {
				// remove the selected trip
				for (var t = 0; t < $scope.trips.length; t++) {
					if ($scope.trips[t]["package"] == package) {
						$scope.trips.splice(t, 1);
						break;
					}
				}
			}
		})
		.error(function(data, status) {
			console.log(data);
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			$rootScope.waiting = false;
		});
	};	
});
