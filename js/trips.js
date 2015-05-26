/**
 * Controls the 'trips' page
 */
app.controller('TripsController', function($scope, $rootScope, $http, $stateParams) {
	debugger;
	/* form the request if package name was supplied */
	$scope.request = {};
	if ($stateParams.package) {
		$scope.request.package = $stateParams.package;
	}
	
	$rootScope.waiting = true;
	$http.post("php/secure/retrieve-orders.php", $scope.request)
	.success(function(data) {
	console.log(data);
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
<<<<<<< HEAD
	});	



	$scope.orders = {};
=======
	});
	
	
	
>>>>>>> c3c3bd6f47a1bbe933cf835ec7f80f9a5c31dfed
	/* remove the orders */
	$scope.removeOrders = function(package) {
		$scope.orders = {};
		$rootScope.waiting = true;
		$scope.orders.email = $rootScope.storage.token.email;
		$scope.orders.package = package;
		console.log("Debug:$scope.orders: ", $scope.orders);

		$http.post("php/secure/remove-orders.php", $scope.orders)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				console.log(data["error"]);
				if (data["error"] == "authentication")
					$rootScope.doSignOut();
				else
					$scope.error = "Error: " + data["error"];
<<<<<<< HEAD
			} else {
				console.log("no error");
				$scope.trips = data["data"];
			}
=======
			} 		
>>>>>>> c3c3bd6f47a1bbe933cf835ec7f80f9a5c31dfed
		})
		.error(function(data, status) {
			console.log(data);
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			$rootScope.waiting = false;
		});
		debugger;

		// Refresh the 'Trips' page
		//$state.go("user.packagesRoot.packages");
				
	};	

});
