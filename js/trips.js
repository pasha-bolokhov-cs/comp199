/**
 * Controls the 'trips' page
 */
app.controller('TripsController', function($scope, $rootScope, $http, $stateParams) {

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
		$scope.orders.email = $rootScope.storage.token.email;
		$scope.orders.package = package;
		
		$http.post("php/secure/remove-orders.php", $scope.orders)
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
			$rootScope.waiting = true;
		});


		// Refresh the 'Trips' page
		//$state.go("user.packagesRoot.packages");
				
	};	

});
