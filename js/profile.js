/**
 * Controls the 'Profile' modal
 */
app.controller('ProfileController', function($scope, $rootScope, $modalInstance, $http) {

	/*
	 * Permanent initialization
	 */
	$scope.stringPattern = /^([a-z]|[0-9]|[\+\-\@.]|\s)*$/i;

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Initialization
		$scope.customer = {};
		$scope.customer.email = $rootScope.customerEmail;
		$scope.showResult = true;
		$scope.error = false;	
		// Indicate password input fields are pristine
		$scope.blurPassword = false;
		$scope.blurPassword2 = false;
	}
	$scope.setup();
	$scope.modify();
	
	/*
	 * Functions assigned to buttons
	 */
	/* 'modify' button in the modal */
	$scope.modify = function() {
		// Indicate we are waiting for data
		$scope.showError = false;
		$scope.waitingPackages = true;
		
		$scope.customer.email = $rootScope.customeremail;
		// Send the request to the PHP script
		$http.post("php/ref-customer", $scope.customer)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				switch(data["error"]) {
				case "name-required":
					$scope.signInForm.name.$setValidity("required", false);
					$scope.signInForm.name.$setDirty();
					break;

				case "password-required":
					$scope.signUpForm.password.$setValidity("required", false);
					$scope.signUpForm.password.$setDirty();
					break;

				case "login":
					$scope.error = "Invalid email or password";
					break;

				default:
					$scope.error = "Error: " + data["error"];
				}
				$scope.showError = true;

				// GG process validation errors
			} else {
				//GG change status to signed in
				$modalInstance.close();
				$token = jwtHelper.decodeToken(data["jwt"]);
				$rootScope.loginName = $token.name;
				$rootScope.customerEmail = $scope.customer.email
				$rootScope.signedIn = true;
				$Scope.customerName = data->name;

				console.log("Got token = ", $token);  //GG
			}
		})
		.error(function(data, status) {
			console.log(data);
			$scope.error = "Error accessing the server: " + status + ".";
			$scope.showError = true;
		})
		.finally(function() { 
			// Indicate that we have an answer
			$scope.waitingPackages = false;
		});
	}

	/* 'Clear' button in the modal */
	$scope.reset = function() {
		/* reset the data */
		$scope.setup();

		$scope.signUpForm.$setPristine();
	}

	/* 'Cancel' button in the modal */
	$scope.cancel = function() {
		$modalInstance.dismiss();
	}
	
	/* dfine signIn from signIn.js*/
	$scope.signIn = function(customerSignIn) {
		signIn();
		angular.module('appSignIn').controller('ProfileController', ProfileController);
	}
});
