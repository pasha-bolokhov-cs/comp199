/**
 * Controls the 'home' page
 */
app.controller('HomeController', function($scope, $state) {

	$scope.interval = 4000;

	$scope.slides = [
		{ image: "images/flip-flops-2.jpg", text: "Dreaming of a different you?",
		  slogan: "Pour a handful of white beach sand on your desk", button: "Start your change" },
		{ image: "images/woman.jpg", text: "Office Work Brings you Nothing but Stress?",
		  slogan: "Dream yourself out of the office cube", button: "Get out now" },
		{ image: "images/traffic-2.jpg", text: "Commuting Feels Like There is No More Air?",
		  slogan: "Get all the air of the world", button: "Start breathing" },
		{ image: "images/starfish-2.jpg", text: "Stars are Calling you?",
		  slogan: "The sky is not too far to reach", button: "Dive now" },
		{ image: "images/xl_GreeceSunset.jpg", text: "Let the Horizon Come to You",
		  slogan: "The airport lights are already blinking", button: "Start your vacation" },
		{ image: "images/albatross.jpg", text:"Albatross Travel ®",
		  slogan: "A Different Measure of Distance", button: "Hop on Our Back" },
	];

	$scope.go = function() {
		$state.go("guest.packagesRoot.packages");
	}
});
