/**
 * Controls the view section of the 'Profile' page
 */
app.controller('ProfileViewController', function($scope, $rootScope, $state, $http, jwtHelper, $localStorage) {

	/*
	 * Permanent initialization
	 */
	$scope.stringPattern = /^([a-z]|[0-9]|[\+\-\@.]|\s)*$/i;

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Initialization
		$rootScope.profile = {};

		$rootScope.profile.email = $rootScope.storage.token.email;
		$rootScope.profile.preemail = $rootScope.profile.email;
		$scope.readOnly = true;
		$scope.passwordStatus = false;	
	}
	$scope.setup();

	/* 
	 * Fetch the profile
	 */
	// Indicate we are waiting for data
	$rootScope.waiting = true;		
	
	// Send the request to the PHP script
	$http.post("php/secure/profile.php")
	.success(function(data) {
		// process the response
		if (data["error"]) {
			switch(data["error"]) {
			case "name-required":
				/* GG this won't work with the parent-state form */
				$scope.profileForm.name.$setValidity("required", false);
				$scope.profileForm.name.$setDirty();
				break;

			case "password-required":
				/* GG this won't work with the parent-state form */
				$scope.profileForm.password.$setValidity("required", false);
				$scope.profileForm.password.$setDirty();
				break;

			case "login":
				/* No user record - logout */
				doSignOut();
				break;

			default:
				$rootScope.error = "Error: " + data["error"];
			}

			// GG process validation errors
			return;
		}

		$rootScope.profile = data["customer"];
	})
	.error(function(data, status) {		
		$rootScope.error = "Error accessing the server: " + status + ".";
	})
	.finally(function() { 
		// Indicate that we have an answer
		$rootScope.waiting = false;
	});

	
	/*
	 * Functions assigned to buttons
	 */
	/* Modify */	
	$scope.modify = function() {
		$state.go("user.profile.modify");
	}
	
	$scope.confirm = function() {
		$scope.readOnly = true;

		// Indicate we are waiting for data
		$rootScope.waiting = true;		
		
		// Send the request to the PHP script
		$http.post("php/secure/confirm.php", $rootScope.profile)
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
				$rootScope.profile.name = data2["name"];
				$rootScope.profile.email = data2["email"];
				$rootScope.profile.birth = data2["birth"];
				$rootScope.profile.nationality = data2["nationality"];			
				$rootScope.profile.passportNo = data2["passportNo"];
				$rootScope.profile.passportExp = data2["passportExp"];
				$rootScope.profile.phone = data2["phone"];			
			}
		})
		.error(function(data2, status) {		
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;		
		});
	}
	
	$scope.passwordChange = function() {	
		$scope.readOnly = true;
		$scope.passwordStatus = true;
		
		$state.go("user.profile.password");	
	}

	$scope.passwordConfirm = function() {
		$scope.readOnly = true;

		// Indicate we are waiting for data
		$rootScope.waiting = true;
        		
		$rootScope.profile.password = $scope.modify.currentpassword;
		$rootScope.profile.password2 = $scope.modify.newpassword;
		// Send the request to the PHP script
		$http.post("php/secure/passwordConfirm.php", $rootScope.profile)
		.success(function(data2) {
			
			// process the response
			if (data2["error"]) {
				switch(data2["error"]) {

				case "password-required":
					$scope.profileForm.password.$setValidity("required", false);
					$scope.profileForm.password.$setDirty();
					break;

				default:
					$rootScope.error = "Error: " + data2["error"];				
				}
				$state.go("user.profile.password");
			} else {
				$scope.readOnly = true;
				$scope.passwordStatus = false;	
				$state.go("guest.home");		
			}
		})
		.error(function(data2, status) {		
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;		
		});		
	}
	
	/* 'Cancel' button */
	$scope.cancel = function() {
	}	
});


/**
 * Controls the modification section of the 'Profile' page
 */
app.controller('ProfileModifyController', function($scope, $rootScope, $state, $http) {

	/*
	 * Permanent initialization
	 */

	/*
	 * Resettable data initialization
	 */

	$scope.confirm = function() {
		// Indicate we are waiting for data
		$rootScope.waiting = true;		
		
		// Send the request to the PHP script
		$http.post("php/secure/update-profile.php", $rootScope.profile)
		.success(function(data) {
			
			// process the response
			if (data["error"]) {
				switch(data["error"]) {
				// This won't work because the profileForm is in the parent state
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

				default:
					$rootScope.error = "Error: " + data["error"];	
				}

				return;
			}

			/* success */
			$rootScope.profile = data["customer"];

			// GGGG update the token if name or email have changed (see "update-profile.php")

			/* go back to the "view" state */
			$state.go("user.profile.view");
		})
		.error(function(data, status) {		
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;		
		});
	}
	
	$scope.passwordChange = function() {	
		$scope.readOnly = true;
		$scope.passwordStatus = true;
		
		$state.go("user.profile.password");	
	}

	$scope.passwordConfirm = function() {
		$scope.readOnly = true;

		// Indicate we are waiting for data
		$rootScope.waiting = true;
        		
		$rootScope.profile.password = $scope.modify.currentpassword;
		$rootScope.profile.password2 = $scope.modify.newpassword;
		// Send the request to the PHP script
		$http.post("php/secure/passwordConfirm.php", $rootScope.profile)
		.success(function(data2) {
			
			// process the response
			if (data2["error"]) {
				switch(data2["error"]) {

				case "password-required":
					$scope.profileForm.password.$setValidity("required", false);
					$scope.profileForm.password.$setDirty();
					break;

				default:
					$rootScope.error = "Error: " + data2["error"];				
				}
				$state.go("user.profile.password");
			} else {
				$scope.readOnly = true;
				$scope.passwordStatus = false;	
				$state.go("guest.home");		
			}
		})
		.error(function(data2, status) {		
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;		
		});		
	}
	
	/* 'Cancel' button */
	$scope.cancel = function() {
	}	
});
