/**
 * Controls the view section of the 'Profile' page
 */
app.controller('ProfileController', function($scope, $rootScope, $state, $http, $localStorage) {
	/*
	 * Permanent initialization
	 */
	$scope.stringPattern = /^([a-z]|[0-9]|[\+\-\@.]|\s)*$/i;

	/*
	 * Resettable data initialization
	 */
	$scope.setup = function() {
		// Initialization
		$scope.profile = {};
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
				$scope.profileForm.name.$setValidity("required", false);
				$scope.profileForm.name.$setDirty();
				break;

			case "password-required":
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

		// Success
		$scope.profile = data["customer"];
		$state.go("user.profile.view");
	})
	.error(function(data, status) {	
		$rootScope.error = "Error accessing the server: " + status + ".";
	})
	.finally(function() { 
		// Indicate that we have an answer
		$rootScope.waiting = false;
	});
});


/**
 * Controls the view section of the 'Profile' page
 */
app.controller('ProfileViewController', function($scope, $state) {
	/*
	 * Functions assigned to buttons
	 */
	/* Modify Profile */	
	$scope.modify = function() {
		$state.go("user.profile.modify");
	}	

	/* Change Password */
	$scope.changePassword = function() {	
		$state.go("user.profile.password");	
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


	/*
	 * Functions assigned to buttons
	 */
	$scope.update = function() {
		// Indicate we are waiting for data
		$rootScope.waiting = true;		
		
		// Send the request to the PHP script
		$http.post("php/secure/update-profile.php", $scope.profile)
		.success(function(data) {
			
			// process the response
			if (data["error"]) {
				switch(data["error"]) {
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
			$scope.profile = data["customer"];

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

	/* 'Cancel' button */
	$scope.cancel = function() {
		$state.go("user.profile.view");
	}
	
	$scope.passwordConfirm = function() {
		// Indicate we are waiting for data
		$rootScope.waiting = true;
        		
		$scope.profile.password = $scope.modify.currentpassword;
		$scope.profile.password2 = $scope.modify.newpassword;
		// Send the request to the PHP script
		$http.post("php/secure/passwordConfirm.php", $scope.profile)
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
});
