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
				$rootScope.error = "Error: " + data["error"];
		} else {
			$scope.trips = data["data"];

			/* obtain merchantId */
			//GG implement
			$scope.merchantId = 'MERCHANT-ID-TO-BE-IMPLEMENTED';
			buttonList = [];
			for (k = 0; k < $scope.trips.length; k++) {
				buttonList.push("trips-paypal-container-" + k.toString());
			}
			$scope.$evalAsync(function() {
				console.log("$evalAsync()");
				paypal.checkout.setup($scope.merchantId, {
					container: buttonList,
					environment: 'sandbox',
					click: $scope.pay,
					accepted: $scope.accepted,
					rejected: $scope.rejected
				});
			});


		}
	})
	.error(function(data, status) {
		console.log(data);
		$rootScope.error = "Error accessing the server: " + status + ".";
	})
	.finally(function() { 
		$rootScope.waiting = false;
	});	


	/*
	 * Functions assigned to buttons
	 */
	/* place an order */
	$scope.pay = function(event, idx) {
		/* Initialize PayPal environment */
		paypal.checkout.initXO();

		/* Send request to the server */
		//GG

		$scope.ecToken = "EC-2PH22151F9744123W";
		paypal.checkout.startFlow($scope.ecToken);

//		paypal.checkout.closeFlow();
	};

	/* order accepted */
	$scope.accepted = function() {
		console.log("order has been accepted");
	};

	/* order rejected or other failure */
	$scope.rejected = function() {
		console.log("order has been rejected");
	};

	/* remove an order */
	$scope.removeOrders = function(package) {
		if (!package)
			return;

		$scope.orders = {};
		$scope.orders.package = package;

		$rootScope.waiting = true;
		$http.post("php/secure/remove-order.php", $scope.orders)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				if (data["error"] == "authentication")
					$rootScope.doSignOut();
				else
					$rootScope.error = "Error: " + data["error"];
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
