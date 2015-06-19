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
		$scope.chg = {};		// password change form
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
			case "authentication":
			case "login":		/* No user record - logout */
				doSignOut();
				return;

			case "name-required":
				$scope.profileForm.name.$setValidity("required", false);
				$scope.profileForm.name.$setDirty();
				break;

			case "password-required":
				$scope.profileForm.password.$setValidity("required", false);
				$scope.profileForm.password.$setDirty();
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
	};	

	/* Change Password */
	$scope.changePassword = function() {	
		$state.go("user.profile.password");	
	};
});


/**
 * Controls the modification section of the 'Profile' page
 */
app.controller('ProfileModifyController', function($scope, $rootScope, $state, $http) {
	/*
	 * Permanent initialization
	 */
	$scope.setup = function() {
		// Save the profile
		$scope.untouchedProfile = JSON.parse(JSON.stringify($scope.profile));
	};
	$scope.setup();


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
				case "authentication":
				case "login":		/* No user record - logout */
					$rootScope.doSignOut();
					return;

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
	};

	/* 'Cancel' button */
	$scope.cancel = function() {
		// revert the profile
		$scope.profile.name = $scope.untouchedProfile.name;
		$scope.profile.birth = $scope.untouchedProfile.birth;
		$scope.profile.nationality = $scope.untouchedProfile.nationality;
		$scope.profile.passportNo = $scope.untouchedProfile.passportNo;
		$scope.profile.passportExp = $scope.untouchedProfile.passportExp;
		$scope.profile.email = $scope.untouchedProfile.email;
		$scope.profile.phone = $scope.untouchedProfile.phone;

		// return to "view" state 
		$state.go("user.profile.view");
	};
});


/**
 * Controls the button section of the password change section of the 'Profile' page
 */
app.controller('ProfilePasswordSubmitController', function($scope, $rootScope, $state, $http) {
	/*
	 * Permanent initialization
	 */


	/*
	 * Resettable data initialization
	 */
	$scope.reset = function() {
		$scope.chg.currPassword = "";
		$scope.chg.newPassword = "";
		$scope.chg.rePassword = "";
	}
	$scope.reset();


	/*
	 * Functions assigned to buttons
	 */
	$scope.passwordSubmit = function() {
		// Indicate we are waiting for data
		$rootScope.waiting = true;

		$scope.request = {};        		
		$scope.request.currPassword = $scope.chg.currPassword;
		$scope.request.newPassword = $scope.chg.newPassword;
		$scope.request.rePassword = $scope.chg.rePassword;
		// Send the request to the PHP script
		$http.post("php/secure/change-password.php", $scope.request)
		.success(function(data) {
			
			// process the response
			if (data["error"]) {
				switch(data["error"]) {
				case "password-wrong":
					$scope.chg.error = "Incorrect password";
					break;

				// GG implement validation errors - all fields
				case "password-required":
					$scope.passwordForm.currPassword.$setValidity("required", false);
					$scope.passwordForm.currPassword.$setDirty();
					break;

				default:
					$rootScope.error = "Error: " + data["error"];				
				}
				return;
			}

			// success
			$state.go("user.profile.view");
		})
		.error(function(data, status) {		
			$rootScope.error = "Error accessing the server: " + status + ".";
		})
		.finally(function() { 
			// Indicate that we have an answer
			$rootScope.waiting = false;		
		});		
	};

	/* 'Cancel' button */
	$scope.cancel = function() {
		$scope.reset();
		// return to "view" state 
		$state.go("user.profile.view");
	};	
});
