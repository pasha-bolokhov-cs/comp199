/**
 * Controls the 'trips' page
 */
app.controller('TripsController', function($scope, $rootScope, $http, $state, $stateParams) {

	/* function which retrieves the orders and updates the list of trips */
	$scope.getOrders = function(newPackage) {
		/* add a request if a package was supplied */
		$scope.request = newPackage ? { package: newPackage } : {};
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
			$scope.unpaidTrips = [];		// only track unpaid trips
			for (k = 0; k < $scope.trips.length; k++) {
				if ($scope.trips[k]["status"] == "Unpaid") {
					buttonList.push("trips-paypal-container-" + k.toString());
					$scope.unpaidTrips.push($scope.trips[k]["package"]);
				}
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
	}
	/* add a package if it was supplied */
	$scope.getOrders($stateParams.package);


	/*
	 * Functions assigned to buttons
	 */
	/* place an order */
	$scope.pay = function(event, idx) {

		/* Initialize PayPal environment */
		paypal.checkout.initXO();

		/* Send request to the server */
		$scope.request = { package: $scope.unpaidTrips[idx] };
		$rootScope.waiting = true;
		$http.post("php/secure/create-payment.php", $scope.request)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				if (data["error"] == "authentication")
					$rootScope.doSignOut();
				else
					$rootScope.error = "Error: " + data["error"];

				paypal.checkout.closeFlow();
				return;
			}

			// success
			if (!data["ec_token"]) {
				$rootScope.error = "Error: no EC-token received";
				paypal.checkout.closeFlow();
				return;
			}
			$scope.ecToken = data["ec_token"];
console.log("GG EC token = ", $scope.ecToken);

			paypal.checkout.startFlow($scope.ecToken);
		})
		.error(function(data, status) {
			console.log(data);
			$rootScope.error = "Error accessing the server: " + status + ".";
			paypal.checkout.closeFlow();
		})
		.finally(function() { 
			$rootScope.waiting = false;
		});			
	};

	/* order accepted */
	$scope.accepted = function(url) {
		console.log("GG order has been accepted with url = ", url);
	
		$scope.paymentRequest = {};
		$scope.paymentRequest.url = url;
		$rootScope.waiting = true;
		$http.post("php/secure/execute-payment.php", $scope.paymentRequest)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				if (data["error"] == "authentication")
					$rootScope.doSignOut();
				else
					$rootScope.error = "Error: " + data["error"];

				return;
			}

			// success - refresh the list of trips
			$scope.getOrders();
			// send confirmation email to customer
			$scope.sendEmail();
		})
		.error(function(data, status) {
			console.log(data);
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			$rootScope.waiting = false;
		});			
	};

	/* send email function */
	$scope.sendEmail = function(){/*
        var conform = [$scope.customer.email, $scope.trips]	
		$scope.conformEmail.email = "lelaywang@gmail.com";
		$scope.conformEmail.merchantId = "hgeugbfvkfsiut3hr";
		if ($scope.merchantId){
			$http.post("php/secure/sendEmail.php", $scope.conformEmail);
			}*/
	};



	/* order rejected or other failure */
	$scope.rejected = function(url) {
		console.log("GG order has been rejected with url = ", url);
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

				return;
			}

			// success - renew the list of orders
			$scope.getOrders();
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
