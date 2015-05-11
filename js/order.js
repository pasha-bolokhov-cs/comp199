app.controller('orderController', function($scope, $http) {

    $scope.name = "";
    $scope.package = "";
    $scope.status = "";
    $scope.purchaseDate = "";
    $scope.receipt = "";
    
    
    $http.post("php/packages.php")
		.success(function() {
        	
		})
		.error(function() {
		    
		})
        
      


});
