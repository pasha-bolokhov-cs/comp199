app.controller('OrdersController', function($scope, $http) {

    $scope.order = {};
    
    $http.post("php/retrieve-orders.php")
		.success(function() {
        	
		})
		.error(function() {
		    
		})

});
