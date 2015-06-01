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
	//GG place this function properly
	$scope.merchantId = 'DYTNTCAVH97J4';
//	window.paypalCheckoutReady = function () { 
//console.log("paypalCheckoutReady()"); //GG
//		paypal.checkout.setup($scope.merchantId, {
//			environment: 'sandbox',
//			container: [ 'checkout-button-0' ]
//		}); 
//	};

	//GG one of the calls is redundant
	$scope.ecToken = "EC-46316657V6786642E";
	paypal.checkout.setup($scope.merchantId, {
		container: 'trips-paypal-container',
		environment: 'sandbox',
		click: function () {
			paypal.checkout.initXO();

			paypal.checkout.startFlow($scope.ecToken);

//			paypal.checkout.closeFlow();
		}
	});

	/* place an order */
	$scope.pay = function(package) {
		if (!package)
			return;

	}

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
