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

			return;
		}

		// success
		$scope.trips = data["data"];

		/* no need to process if there are no orders */
		if ($scope.trips.length == 0)
			return;

		/* obtain merchantId */
		if (!data["merchant_id"]) {
			$rootScope.error = "Error: server did not provide Merchant Id";
			return;
		}
		$scope.merchantId = data["merchant_id"];
		/* create the list of button id's */
		buttonList = [];
		for (k = 0; k < $scope.trips.length; k++) {
			buttonList.push("trips-paypal-container-" + k.toString());
		}
		/* defer initialization of paypal */
		$scope.$applyAsync(function() {
			paypal.checkout.setup($scope.merchantId, {
				container: buttonList,
				environment: 'sandbox',
				click: $scope.pay,
				accepted: $scope.accepted,
				rejected: $scope.rejected
			});
		});
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
		$scope.request = { package: $scope.trips[idx].package };
		$rootScope.waiting = true;
		$http.post("php/secure/create-payment.php", $scope.request)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				if (data["error"] == "authentication")
					$rootScope.doSignOut();
				else
					$rootScope.error = "Error: " + data["error"];

				return;
			}

			// success
			if (!data["ec_token"]) {
				$rootScope.error = "Error: no EC-token received";
				return;
			}
			$scope.ecToken = data["ec_token"];
console.log("GG EC token = ", $scope.ecToken);


			paypal.checkout.startFlow($scope.ecToken);

//			paypal.checkout.closeFlow();
		})
		.error(function(data, status) {
			console.log(data);
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			$rootScope.waiting = false;
		});			
	};

	/* order accepted */
	$scope.accepted = function(url) {
		console.log("order has been accepted with url = ", url);
	};

	/* order rejected or other failure */
	$scope.rejected = function(url) {
		console.log("order has been rejected with url = ", url);
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
