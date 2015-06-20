//Forest fire

//Growth and Lighting probabilities
const GROWTH = 0.00000005;
const FIRE = 0.00000000000001;

var REGION_WIDTH = 100;
var REGION_HEIGHT = 100;

//update the state of the model
function onUpdate(colorb){

	//console.log(colorb);
	//console.log("Region: "+region);
    return ++colorb;
}

function render(){
	//Displaying updated state
}

//Initialises the region with basic trees
function initRegion(){

}

/*						*/
/*	Tree object 		*/
/*						*/
function getTree(){

	tree = {fireLevel: 0, growthLevel: 0, isAlive: false};
	return tree;
}

//Once the document is loaded properly, run onUpdate.
$( document ).ready(function() {

	//Region is the game board
	var region = new Array(REGION_WIDTH);

	for(var i = 0; i < region.length; i++){

		region[i] = new Array(REGION_HEIGHT);
		for(var j = 0; j < region[i].length; j++){
			region[i][j] = getTree();
		}
	}

	console.log(region[0]);

	var color = 0;
	setInterval(function(){color = onUpdate(color)}, 17);

});