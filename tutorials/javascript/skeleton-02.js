$( document ).ready(function() {

	var canvas = document.getElementById("canvas");

	canvas.addEventListener("mousedown", function (e){
		console.log("Mouse down.");
	}, false);

	canvas.addEventListener("mouseup", function (e){
		console.log("Mouse up.");
	}, false);
	console.log("Whats uuuup!");
});