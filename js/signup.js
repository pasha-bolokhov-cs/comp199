/**
 * Controls the 'Sign Up' modal
 */
app.controller('SignUpController', function($scope, $rootScope, $modalInstance, $http, jwtHelper, $localStorage) {

	/*
	 * Permanent initialization
	 */

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Initialization
		$scope.customer = {};
		$scope.error = false;
		$scope.waiting = false;

		// Indicate password input fields are pristine
		$scope.blurPassword = false;
		$scope.blurPassword2 = false;
	}
	$scope.setup();

	/*
	 * Functions assigned to buttons
	 */
	/* 'Sign-up' button in the modal */
	$scope.signUp = function() {
		$scope.waiting = true;

		// Send the request to the PHP script
		$http.post("php/signup.php", $scope.customer)
		.success(function(data) {
			// process the response
			if (data["error"]) {
				switch (data["error"]) {
				case "name-required":
					$scope.signUpForm.name.$setValidity("required", false);
					$scope.signUpForm.name.$setDirty();
					break;
				case "name-wrong":
					$scope.signUpForm.name.$setValidity("pattern", false);
					$scope.signUpForm.name.$setDirty();
					break;

				case "birth-required":
					$scope.signUpForm.birth.$setValidity("required", false);
					$scope.signUpForm.birth.$setDirty();
					break;

				case "nationality-required":
					$scope.signUpForm.nationality.$setValidity("required", false);
					$scope.signUpForm.nationality.$setDirty();
					break;
				case "nationality-wrong":
					$scope.signUpForm.nationality.$setValidity("pattern", false);
					$scope.signUpForm.nationality.$setDirty();
					break;

				case "passportNo-required":
					$scope.signUpForm.passportNo.$setValidity("required", false);
					$scope.signUpForm.passportNo.$setDirty();
					break;
				case "passportNo-wrong":
					$scope.signUpForm.passportNo.$setValidity("pattern", false);
					$scope.signUpForm.passportNo.$setDirty();
					break;

				case "passportExp-required":
					$scope.signUpForm.passportExp.$setValidity("required", false);
					$scope.signUpForm.passportExp.$setDirty();
					break;

				case "email-required":
					$scope.signUpForm.email.$setValidity("required", false);
					$scope.signUpForm.email.$setDirty();
					break;
				case "email-wrong":
					$scope.signUpForm.email.$setValidity("pattern", false);
					$scope.signUpForm.email.$setDirty();
					break;
				case "email-exists":
					$scope.signUpForm.email.$setValidity("exists", false);
					$scope.signUpForm.email.$setDirty();
					break;

				case "password-required":
					$scope.signUpForm.password.$setValidity("required", false);
					$scope.signUpForm.password.$setDirty();
					break;

				default:
					$scope.error = "Error: " + data["error"];
				}
				return;
			}
			// Check the token
			if (!$rootScope.storage.jwt || !$rootScope.storage.token) {
				$modalInstance.dismiss("no token");
				$rootScope.error = "Error during sign-up: no token";
				return;
			}
			if (!$rootScope.storage.token["email"]) {
				$modalInstance.dismiss("incomplete token");
				$rootScope.error = "Error during sign-up: incomplete token";
				return;
			}

			// Close the window
			$modalInstance.close();

			// Change to the 'signed in' state
			$rootScope.doSignIn();
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

});
