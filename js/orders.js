app.controller('OrdersController', function($scope, $http) {

    $scope.order = {};
    
    $http.post("php/orders.php")
		.success(function() {
        	
		})
		.error(function() {
		    
		})

});
