
/**
 * Controls the 'Sign In' modal
 */
app.controller('SignInController', function($scope, $rootScope, $modalInstance, $http,
					    $state) {

	/*
	 * Permanent initialization
	 */
	$scope.errorMessages = {
		"email-required": "Email is required",
		"email-wrong": "Incorrect email address",
		"password-required": "Password is required",
		"login": "Invalid email or password"
	};

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
			if (data && data.error) {
				switch (data.error) {
				case "email-required":
				case "email-wrong":
				case "password-required":
				case "login":
					$scope.error = $scope.errorMessages[data.error];
					break;

				default:
					$scope.error = "Error: " + data.error;
				}
				$scope.signInForm.$setPristine();
				return;
			}

			/* Success - check the token */
			if (!$rootScope.storage.jwt || !$rootScope.storage.token) {
				$modalInstance.dismiss("no token");
				$rootScope.error = "Error during sign-in: no token";
				return;
			}
			if (!$rootScope.storage.token["email"]) {
				$modalInstance.dismiss("incomplete token");
				$rootScope.error = "Error during sign-in: incomplete token";
				return;
			}

			/* Token acceptable - close the modal */
			$modalInstance.close();
		})
		.error(function(data, status) {
			console.log(data);
			$scope.error = "Error accessing the server: " + status + ".";
			$scope.signInForm.$setPristine();
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
