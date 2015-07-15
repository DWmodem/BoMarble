//Forest fire

//Growth and Lighting probabilities
//1 means event always occurs. 100%.
//0.01 means event occurs 1% of frames
//Fire is only applied to trees with a non-zero growth value
//Fire cannot spawn on empty ground

var GROWTH_RATE = 0.005;
var FIRE_RATE = 0.00001;
var MAX_TREE_SIZE = 10;
var REGION_WIDTH = 150;
var REGION_HEIGHT = 150;

var REGION_HEIGHT_LIMIT = REGION_HEIGHT-1;
var REGION_WIDTH_LIMIT = REGION_WIDTH-1;

var isDebugging = false;
//update the state of the model
function onUpdate(region, engine){

	region = growRegion(region);
	region = fireSpreads(region);
	region = flameRegion(region);
	return region;
}

//Display the state of the region to the viewer
function render(region, engine){
	
	requestAnimationFrame(function(){
		render(region, engine);
	});

	engine.printRegion(region);
	//drawing code goes here
}


//Determine if an even should occur, in function of a supplied probability (1 = 100%, 0.01 = 1%)
function eventOccurs(probabilityValue){
	
	if(probabilityValue > Math.random()){
		return true;
	}

	return false;
}

//populates the region with growin trees
//Occurs once per frame
function growRegion(region){

	for(var m = 0; m < region.length; m++){
		for(var n = 0; n < region[m].length; n++){
			
			//Grow the tree if it has started growing, but not at max size
			if((region[m][n].growthLevel > 0 ) && (region[m][n].growthLevel < MAX_TREE_SIZE) && (region[m][n].fireLevel <= 0)){
				region[m][n].growthLevel++;
				region[m][n].isAlive = true;
				continue;

			//Grow a new tree! If the growth rate allows it
			} else if((region[m][n].growthLevel === 0) && (eventOccurs(GROWTH_RATE))) {
				region[m][n].growthLevel++;
			}
		}
	}
	return region;
}

//Poupulates the region with fire.
//Fire only occurs on trees, and tends to be less probable
//Occurs once per frame 
function flameRegion(region){
	
	for(var m = 0; m < region.length; m++){
		for(var n = 0; n < region[m].length; n++){
			
			//If there is a tree, lighting strikes! (if eventOccurs)
			if(eventOccurs(FIRE_RATE) && (region[m][n].growthLevel > 0) && (region[m][n].fireLevel <= 0 )){
				region[m][n].fireLevel = 1;
			}
		}
	}
	return region;
}

//Trees spread fire to their immediate neighbours!
function fireSpreads(region){

	for(var m = 0; m < region.length; m++){
		for(var n = 0; n < region[m].length; n++){
			
			//If there is a tree, lighting strikes! (if eventOccurs)
			//Make sure not to set fire to an element outside of the region
			if(region[m][n].fireLevel >= 1){

				//Top left
				if(((m-1) >= 0 ) && ((n-1) >= 0 )){
					topLeftTree = region[m-1][n-1];
					dbpr("Flaming region["+(m-1)+"]["+(n-1)+"]");
					region[m-1][n-1] = catchFire(topLeftTree);
				}
				
				//Directly top
				if((m-1) >= 0){
					topTree = region[m-1][n];
					dbpr("Flaming region["+(m-1)+"]["+(n)+"]");
					region[m-1][n] = catchFire(topTree);
				}
				
				//Top right
				if(((m-1) >= 0) && ((n+1) <= REGION_HEIGHT_LIMIT)){
					topRightTree = region[m-1][n+1];
					dbpr("Flaming region["+(m-1)+"]["+(n+1)+"]");
					region[m-1][n+1] = catchFire(topRightTree);
				}
				
				//Left
				if((n-1) >= 0){
					leftTree = region[m][n-1];
					dbpr("Flaming region["+m+"]["+(n-1)+"]");
					region[m][n-1] = catchFire(leftTree);
				}

				//Right
				if((n+1) <= REGION_HEIGHT_LIMIT){
					rightTree = region[m][n+1];
					dbpr("Flaming region["+m+"]["+(n+1)+"]");
					region[m][n+1] = catchFire(rightTree);
				}
				
				//Bottom left
				if(((m+1) <= REGION_WIDTH_LIMIT) && ((n-1) >= 0)){
					bottomLeftTree = region[m+1][n-1];
					dbpr("Flaming region["+(m+1)+"]["+(n-1)+"]");
					region[m+1][n-1] = catchFire(bottomLeftTree);
				}
				
				//Directly bottom
				if((m+1) <= REGION_WIDTH_LIMIT){
					bottomTree = region[m+1][n];
					dbpr("Flaming region["+(m+1)+"]["+n+"]");
					region[m+1][n] = catchFire(bottomTree);
				}
				
				//Bottom right
				if(((m+1) <= REGION_WIDTH_LIMIT) && ((n+1) <= REGION_HEIGHT_LIMIT)){
					bottomRightTree = region[m+1][n+1];
					dbpr("Flaming region["+(m+1)+"]["+(n+1)+"]");
					region[m+1][n+1] = catchFire(bottomRightTree);
				}
			}
		}
	}
	return region;
}

/*						*/
/*	Tree object 		*/
/*						*/
function getTree(){

	tree = {fireLevel: 0, growthLevel: 0, isAlive: false};
	return tree;
}

function getRegion(){

	//MxN
	//width by height
	console.log("Starting region creation");
	var region = new Array(REGION_WIDTH);
	for(var m = 0; m < region.length; m++){

		region[m] = new Array(REGION_HEIGHT);
		for(var n = 0; n < region[m].length; n++){
			
			region[m][n] = getTree();
		}
	}
	console.log("Ended region creation. Returning region.");
	return region;
}

function catchFire(tree){

	if(tree.growthLevel === 0){
		tree.fireLevel = 0;
		tree.isAlive = false;
		return tree;
	}

	tree.fireLevel++;
	tree.growthLevel--;
	return tree;
}


//Once the document is loaded properly, run onUpdate.
$( document ).ready(function() {

	console.log("Forestfire loaded");
	bobRoss = new RenderEngine();

	//Region is the game board
	//Display region is the region selected for printout. Strictly speaking this does nothing, since region and displayRegion are the same reference anyways
	var region = getRegion();
	var displayRegion = region;

	setInterval(function(){
		region = onUpdate(region, bobRoss);
		displayRegion = region;
	}, 17);

//	render(displayRegion, bobRoss);
	initCanvas($("#primaryCanvas")[0], REGION_WIDTH, REGION_HEIGHT);
});