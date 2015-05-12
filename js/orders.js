app.controller('ordersController', function($scope, $http) {

    $scope.name = "";
    $scope.package = "";
    $scope.status = "";
    $scope.purchaseDate = "";
    $scope.receipt = "";
    
    
    $http.post("php/orders.php")
		.success(function() {
        	
		})
		.error(function() {
		    
		})

});
