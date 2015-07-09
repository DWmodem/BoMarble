//Render engine
//Takes care of all things displaying
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

//Boundary is the spacing in between squares
//m is the number of squares high (number of rows)
//n is the number of squares wide (number of columns)
function initCanvas(n, m, boundary, canvas){
	width = canvas.width;
	height = canvas.height;

	// width/(n+2) = number of pixels wide
}