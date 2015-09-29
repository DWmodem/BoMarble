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
/*
	Three.js "tutorials by example"
	Author: Lee Stemkoski
	Date: July 2013 (three.js v59dev)
 */
	
//////////	
// MAIN //
//////////
// standard global variables
var container, scene, camera, renderer, controls, stats;
var keyboard = new THREEx.KeyboardState();
var clock = new THREE.Clock();

// custom global variables
var cube;

// initialization
init();
initDummies(12, 12, 25);

// animation loop / game loop
animate();
///////////////
// FUNCTIONS //
///////////////

//We're going to have a number of dummies equal to the size of our forest.
//One for each tree, and one for each fire. We're going to turn them on and off
function initDummies(m, n, size){
	
	for(var i = 0; i < m; i++ ){
		for(var j = 0; j < n; j++){
			scene.add(getCube("red", j, i, size));

		}
	}
	
}

function getCube(color, x, z, size){
	
	var cubeMaterials = new THREE.MeshBasicMaterial( { color: 0xff3433 } );

	// Cube parameters: width (x), height (y), depth (z), 
	//        (optional) segments along x, segments along y, segments along z
	var cubeGeometry = new THREE.CubeGeometry( size, size, size, 1, 1, 1 );

	// using THREE.MeshFaceMaterial() in the constructor below
	//   causes the mesh to use the materials stored in the geometry
	cube = new THREE.Mesh( cubeGeometry, cubeMaterials );
	cube.position.set(x*size, size/2, z*size);
	return cube;	
}

function init() 
{
	///////////
	// SCENE //
	///////////
	scene = new THREE.Scene();
	////////////
	// CAMERA //
	////////////
	
	// set the view size in pixels (custom or according to window size)
	// var SCREEN_WIDTH = 400, SCREEN_HEIGHT = 300;
	var SCREEN_WIDTH = window.innerWidth, SCREEN_HEIGHT = window.innerHeight;	

	// camera attributes
	var VIEW_ANGLE = 45, ASPECT = SCREEN_WIDTH / SCREEN_HEIGHT, NEAR = 0.1, FAR = 20000;

	// set up camera
	camera = new THREE.PerspectiveCamera( VIEW_ANGLE, ASPECT, NEAR, FAR);

	// add the camera to the scene
	scene.add(camera);

	// the camera defaults to position (0,0,0)
	// 	so pull it back (z = 400) and up (y = 100) and set the angle towards the scene origin
	camera.position.set(0,150,400);
	camera.lookAt(scene.position);	
	
	//////////////
	// RENDERER //
	//////////////
	
	// create and start the renderer; choose antialias setting.
	if ( Detector.webgl )
		renderer = new THREE.WebGLRenderer( {antialias:true} );
	else
		renderer = new THREE.CanvasRenderer(); 
	
	renderer.setSize(SCREEN_WIDTH, SCREEN_HEIGHT);
	
	// attach div element to variable to contain the renderer
	container = document.getElementById( 'ThreeJS' );

	// alternatively: to create the div at runtime, use:
	//   container = document.createElement( 'div' );
	//    document.body.appendChild( container );
	
	// attach renderer to the container div
	container.appendChild( renderer.domElement );
	
	////////////
	// EVENTS //
	////////////
	// automatically resize renderer
	THREEx.WindowResize(renderer, camera);

	// toggle full-screen on given key press
	THREEx.FullScreen.bindKey({ charCode : 'm'.charCodeAt(0) });
	
	//////////////
	// CONTROLS //
	//////////////
	// move mouse and: left   click to rotate, 
	//                 middle click to zoom, 
	//                 right  click to pan
	controls = new THREE.OrbitControls( camera, renderer.domElement );
	
	///////////
	// STATS //
	///////////
	
	// displays current and past frames per second attained by scene
	stats = new Stats();
	stats.domElement.style.position = 'absolute';
	stats.domElement.style.bottom = '0px';
	stats.domElement.style.zIndex = 100;
	container.appendChild( stats.domElement );
	
	///////////
	// LIGHT //
	///////////
	
	// create a light
	var light = new THREE.PointLight(0xffffff);
	light.position.set(0,250,0);
	scene.add(light);
	var ambientLight = new THREE.AmbientLight(0x111111);
	// scene.add(ambientLight);
	
	//////////////
	// GEOMETRY //
	//////////////
		

	var cubeMaterials = new THREE.MeshBasicMaterial( { color: 0xff3333 } );
	// Cube parameters: width (x), height (y), depth (z), 
	//        (optional) segments along x, segments along y, segments along z
	var cubeGeometry = new THREE.CubeGeometry( 25, 25, 25, 1, 1, 1 );

	// using THREE.MeshFaceMaterial() in the constructor below
	//   causes the mesh to use the materials stored in the geometry
	cube = new THREE.Mesh( cubeGeometry, cubeMaterials );
	cube.position.set(-100, 12, -50);
	scene.add( cube );		

	// create a set of coordinate axes to help orient user
	//    specify length in pixels in each direction
	var axes = new THREE.AxisHelper(100);
	scene.add( axes );
	
	///////////
	// FLOOR //
	///////////
	
	// note: 4x4 checkboard pattern scaled so that each square is 25 by 25 pixels.
	var floorTexture = new THREE.ImageUtils.loadTexture( '../../images/checkerboard.jpg' );
	floorTexture.wrapS = floorTexture.wrapT = THREE.RepeatWrapping; 
	floorTexture.repeat.set( 10, 10 );
	// DoubleSide: render texture on both sides of mesh
	var floorMaterial = new THREE.MeshBasicMaterial( { map: floorTexture, side: THREE.DoubleSide } );
	var floorGeometry = new THREE.PlaneGeometry(1000, 1000, 1, 1);
	var floor = new THREE.Mesh(floorGeometry, floorMaterial);
	floor.position.y = -0.5;
	floor.rotation.x = Math.PI / 2;
	scene.add(floor);
	
	/////////
	// SKY //
	/////////
	
	// recommend either a skybox or fog effect (can't use both at the same time) 
	// without one of these, the scene's background color is determined by webpage background
	// make sure the camera's "far" value is large enough so that it will render the skyBox!
	var skyBoxGeometry = new THREE.CubeGeometry( 10000, 10000, 10000 );
	// BackSide: render faces from inside of the cube, instead of from outside (default).
	var skyBoxMaterial = new THREE.MeshBasicMaterial( { color: 0x9999ff, side: THREE.BackSide } );
	var skyBox = new THREE.Mesh( skyBoxGeometry, skyBoxMaterial );
	// scene.add(skyBox);
	
	// fog must be added to scene before first render
	scene.fog = new THREE.FogExp2( 0x9999ff, 0.00025 );
}
function animate() 
{
    requestAnimationFrame( animate );
	render();
	update();
}
function update()
{
	// delta = change in time since last call (in seconds)
	var delta = clock.getDelta(); 
	// functionality provided by THREEx.KeyboardState.js
	if ( keyboard.pressed("1") )
		document.getElementById('message').innerHTML = ' Have a nice day! - 1';	
	if ( keyboard.pressed("2") )
		document.getElementById('message').innerHTML = ' Have a nice day! - 2 ';	
		
	controls.update();
	stats.update();
}

function render() 
{	
	renderer.render( scene, camera );
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