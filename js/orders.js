app.controller('OrdersController', function($scope, $http) {

    $scope.order = {};
    
    $http.post("php/secure/retrieve-orders.php")
		.success(function() {
        	
		})
		.error(function() {
		    
		})

});
