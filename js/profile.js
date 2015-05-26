/**
 * Controls the 'Profile' modal
 */
app.controller('ProfileController', function($scope, $rootScope, $http, jwtHelper, $localStorage) {

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
		$scope.customer.email = $rootScope.storage.token.email;
		$scope.customer.preemail = $scope.customer.email;
		$scope.showResult = true;
		$scope.error = false;
		$scope.readOn = true;
		
		// Indicate password input fields are pristine
		$scope.blurPassword = false;
		$scope.blurPassword2 = false;
	}
	$scope.setup();
	
	/*
	 * Functions assigned to buttons
	 */
	/* 'profile' in the modal */

	$scope.profile = function() {
		// Indicate we are waiting for data
		$scope.showError = false;
		$scope.waitingPackages = true;		
		
		// Send the request to the PHP script
		$http.post("php/secure/profile.php", $scope.customer)
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

				$scope.customer.name = data["name"];
				$scope.customer.email = data["email"];
				$scope.customer.birth = data["birth"];
				$scope.customer.nationality = data["nationality"];			
				$scope.customer.passportNo = data["passportNo"];
				$scope.customer.passportExp = data["passportExp"];
				$scope.customer.phone = data["phone"];
				
			}
		})
		.error(function(data, status) {		
			$scope.error = "Error accessing the server: " + status + ".";
			$scope.showError = true;
		})
		.finally(function() { 
			// Indicate that we have an answer
			$scope.waitingPackages = false;
		});
	}
	$scope.profile();
	/* modify function */	
	$scope.modify = function() {
		$scope.readOn = false;		
	}

	$scope.confirm = function() {
		$scope.readOn = true;
			
		// Indicate we are waiting for data
		$scope.showError = false;
		$scope.waitingPackages = true;		
		
		// Send the request to the PHP script
		$http.post("php/secure/confirm.php", $scope.customer)
		.success(function(data2) {
	
			// process the response
			if (data2["error"]) {
				switch(data2["error"]) {
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
					$scope.error = "Error: " + data2["error"];
				}
				$scope.showError = true;
			} else {
				$rootScope.doSignOut();
			}
		})
		.error(function(data2, status) {		
			$scope.error = "Error accessing the server: " + status + ".";
			$scope.showError = true;
		})
		.finally(function() { 
			// Indicate that we have an answer
			$scope.waitingPackages = false;		
		});
		
	}
});