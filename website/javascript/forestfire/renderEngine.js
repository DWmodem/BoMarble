//Render engine
//Takes care of all things displaying
console.log("Render file open");
function RenderEngine(canvas){

	this.canvas = canvas;

	this.doThing = function() {
		alert("Thing done!");
	};

	this.printRegion = function(region){

		console.log("Printing region!");

		for(var m = 0; m < region.length; m++){

			var app = "M: "+m;
			for(var n = 0; n < region[m].length; n++){
				
				var g = region[m][n].growthLevel;
				if(g == 10){
					g = "A";
				}
				app += " "+g;
			}
			console.log(app);	
		}
	};

	this.canvasPrint = function(region){

	};
}

function dbpr(string){
	if(isDebugging){
		console.log(string);
	}
}

//m is the number of squares high (number of rows)
//n is the number of squares wide (number of columns)
//Takes a region, and prints the state of every square onto the canvas.
//new comment!
function updateCanvas(canvas, m, n, region){

	width = canvas.width;
	height = canvas.height;

	squareWidth = Math.floor(width/m);	//We want the width in pixels, so whole numbers only
	squareHeight = Math.floor(height/n);
	ctx = canvas.getContext("2d");

	var color = "white";

	for(var i = 0; i < m; i++){
		for(var j = 0; j < n; j++){

			if(region[i][j].growthLevel > 1){
				color = "green";
			} else if(region[i][j].fireLevel > 0){
				color = "red";
			} else {
				color = "white";
			}

			ctx.beginPath();
			ctx.fillStyle = color;
			ctx.rect( i*squareWidth , j*squareHeight, squareWidth, squareHeight);		//rect(x, y, width, height);
			ctx.fill();
		}
	}
}

//50% chance to return true, 50% chance to return false.
function flipCoin(odds){

	if(typeof odds 	== 'undefined'){
		odds = 0.5;
	}

	if(Math.random() < odds){
		return true;
	}

	return false;
}