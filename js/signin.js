
/**
 * Controls the 'Sign In' modal
 */
app.controller('signInController', function($scope, $rootScope, $modalInstance, $http) {

	/*
	 * Permanent initialization
	 */
	$rootScope.onPackagesPage = true;
	$scope.stringPattern = /^([a-z]|[0-9]|[\+\-\@.]|\s)*$/i;

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Initialization
		$scope.customer = {};
		$scope.showError = false;
		$scope.error = false;
		$scope.waitingPackages = false;
	}
	$scope.setup();

	/*
	 * Functions assigned to buttons
	 */
	/* 'Sign-In' button in the modal */
	$scope.signIn = function() {
		// Indicate we are waiting for data
		$scope.waitingPackages = true;

		// Send the request to the PHP script
		$http.post("php/signin_saveStatus.php", $scope.customer)
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

				default:
					$scope.error = "Error: " + data["error"];
				}

				// GG process validation errors
			} else {
				//GG change status to signed in
				$modalInstance.close();
				$rootScope.signedIn = true;

			}
		})
		.error(function(data, status) {
			console.log(data);
			$scope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$scope.waitingPackages = false;
		});
	}

	/* 'Cancel' button in the modal */
	$scope.cancel = function() {
		$modalInstance.dismiss();
	}

});

