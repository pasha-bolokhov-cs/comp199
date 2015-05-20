/**
 * Controls the 'trips' page
 */
app.controller('OrdersController', function($scope, $http, $stateParams) {

	if ($stateParams.package) 
		console.log("OrdersController(): got package name = `" + $stateParams.package + "'");
	else
		console.log("OrdersController(): got invoked without a package");

	$scope.order = {};

	/* 
	 * Need to submit $stateParams.package (the name of the chosen package) to the Php script
	 * The Php script should add this package to the user's cart if it's not there already
	 * The check can be accomplished perhaps by making the combination "customerId, packageId" Unique
	 */    
//	$http.post("php/secure/retrieve-orders.php")
//	.success(function() {
//	})
//	.error(function() {
//	});

});
