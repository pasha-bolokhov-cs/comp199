/**
 * Controls the 'home' page
 */
app.controller('HomeController', function($scope, $state) {

	$scope.interval = 4000;

	$scope.slides = [
		{ image: "images/woman.jpg", text: "Office Work Brings you Nothing but Stress?",
		  slogan: "Dream yourself out of the office cube", button: "Get out now" },
		{ image: "images/traffic-2.jpg", text: "Commuting Feels Like There is No More Air?",
		  slogan: "Get all the air of the world", button: "Start breathing" },
		{ image: "images/flip-flops-2.jpg", text: "Dreaming of a different you?",
		  slogan: "Pour a handful of sand on your desk", button: "Star your change" },
		{ image: "images/starfish-2.jpg", text: "Stars are Calling upon you?",
		  slogan: "Nothing is too far to reach", button: "Dive now" },
		{ image: "images/xl_GreeceSunset.jpg", text: "Let the Horizon Come to You",
		  slogan: "The airport lights are already blinking", button: "Start your vacation" }
	];

	$scope.go = function() {
		$state.go("guest.packagesRoot.packages");
	}
});
