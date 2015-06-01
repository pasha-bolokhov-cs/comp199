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
		$scope.readOnly = true;		
		
		// Indicate password input fields are pristine
		$scope.blurPassword = false;
		$scope.blurPassword2 = false;
	}
	$scope.setup();
	
	/*
	 * Functions assigned to buttons
	 */
		debugger;	
		$scope.profile = function() {
		// Indicate we are waiting for data
		$rootScope.waiting = true;		
		
		// Send the request to the PHP script
		$http.post("php/secure/profile.php", $scope.customer)
		.success(function(data) {
	
			// process the response
			if (data["error"]) {
				switch(data["error"]) {
				case "name-required":
					$scope.profileForm.name.$setValidity("required", false);
					$scope.profileForm.name.$setDirty();
					break;

				case "password-required":
					$scope.profileForm.password.$setValidity("required", false);
					$scope.profileForm.password.$setDirty();
					break;

				case "login":
					$scope.error = "Invalid email or password";
					break;

				default:
					$rootScope.error = "Error: " + data["error"];
				}

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
				$scope.customer.password = "";
				$scope.customer.password2 = "";				
			}
		})
		.error(function(data, status) {		
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;
		});
	}
	$scope.profile();
	
	/* modify function */	
	$scope.modify = function() {
		$scope.readOnly = false;
		
		$scope.customer.password = "";
		$scope.customer.password2 = "";
	}
	
	$scope.confirm = function() {
		$scope.readOnly = true;

		// Indicate we are waiting for data
		$rootScope.waiting = true;		
		
		// Send the request to the PHP script
		$http.post("php/secure/confirm.php", $scope.customer)
		.success(function(data2) {
			
			// process the response
			if (data2["error"]) {
				switch(data2["error"]) {
				case "name-required":
					$scope.profileForm.name.$setValidity("required", false);
					$scope.profileForm.name.$setDirty();
					break;
				case "name-wrong":
					$scope.profileForm.name.$setValidity("pattern", false);
					$scope.profileForm.name.$setDirty();
					break;

				case "birth-required":
					$scope.profileForm.birth.$setValidity("required", false);
					$scope.profileForm.birth.$setDirty();
					break;

				case "nationality-required":
					$scope.profileForm.nationality.$setValidity("required", false);
					$scope.profileForm.nationality.$setDirty();
					break;
				case "nationality-wrong":
					$scope.profileForm.nationality.$setValidity("pattern", false);
					$scope.profileForm.nationality.$setDirty();
					break;

				case "passportNo-required":
					$scope.profileForm.passportNo.$setValidity("required", false);
					$scope.profileForm.passportNo.$setDirty();
					break;
				case "passportNo-wrong":
					$scope.profileForm.passportNo.$setValidity("pattern", false);
					$scope.profileForm.passportNo.$setDirty();
					break;

				case "passportExp-required":
					$scope.profileForm.passportExp.$setValidity("required", false);
					$scope.profileForm.passportExp.$setDirty();
					break;

				case "email-required":
					$scope.profileForm.email.$setValidity("required", false);
					$scope.profileForm.email.$setDirty();
					break;
				case "email-wrong":
					$scope.profileForm.email.$setValidity("pattern", false);
					$scope.profileForm.email.$setDirty();
					break;
				case "email-exists":
					$scope.profileForm.email.$setValidity("exists", false);
					$scope.profileForm.email.$setDirty();
					break;

				case "password-required":
					$scope.profileForm.password.$setValidity("required", false);
					$scope.profileForm.password.$setDirty();
					break;

				default:
					$rootScope.error = "Error: " + data2["error"];				
				}
			} else {
				$scope.customer.name = data2["name"];
				$scope.customer.email = data2["email"];
				$scope.customer.birth = data2["birth"];
				$scope.customer.nationality = data2["nationality"];			
				$scope.customer.passportNo = data2["passportNo"];
				$scope.customer.passportExp = data2["passportExp"];
				$scope.customer.phone = data2["phone"];

//// GG
//// Profile page should not log the customer in
//// or save new token
////
////				/* Fetch the token */				
////				if (!data2["jwt"]) 
////					return;
////				$token = jwtHelper.decodeToken(data2["jwt"]);
////
////				/* Save the token */
////				$localStorage.token = $token;
////				$localStorage.jwt = data2["jwt"];
////
////				// Change to the 'signed in' state
////				$rootScope.doSignIn();				
			}
		})
		.error(function(data2, status) {		
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;		
		});
		debugger;
		//$scope.setup();
	}
});