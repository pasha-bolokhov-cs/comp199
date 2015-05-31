
/**
 * Controls the 'Sign In' modal
 */
app.controller('SignInController', function($scope, $rootScope, $modalInstance, $http,
					    jwtHelper, $state, $localStorage) {

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
		$scope.error = false;
		$scope.waiting = false;
	}
	$scope.setup();

	/*
	 * Functions assigned to buttons
	 */
	/* 'Sign-In' button in the modal */
	$scope.signIn = function() {
		// Indicate we are waiting for data
		$scope.waiting = true;

		// Send the request to the PHP script
		$http.post("php/signin.php", $scope.customer)
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
			} else {
				/* Success */
				$modalInstance.close();

				if (!data["jwt"])
					return;
				$token = jwtHelper.decodeToken(data["jwt"]);

				console.log("signIn controller(): Got token = ", $token);  //GG
				/* Save the token */
				$localStorage.token = $token;
				$localStorage.jwt = data["jwt"];
			}
		})
		.error(function(data, status) {
			console.log(data);
			$scope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$scope.waiting = false;
		});
	}

	/* 'Cancel' button in the modal */
	$scope.cancel = function() {
		$modalInstance.dismiss();
	}

});
