var scene = new THREE.Scene();

//PerspectiveCamera(fieldOfView, aspectRatio, clippingDistanceNear, clippingDistanceFar) Clipping distance will tell when to stop rendering visible objects
var camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);

var renderer = new THREE.WebGLRenderer();
renderer.setSize( window.innerWidth, window.innerHeight);

document.body.appendChild(renderer.domElement);

var geometry = new THREE.BoxGeometry(1, 5, 2);
var material = new THREE.MeshBasicMaterial({color: 0xfff300});
var cube = new THREE.Mesh(geometry, material);
var wireframe = new THREE.WireframeHelper(cube, 0xff0000);
var counter = 0;
scene.add(cube);
scene.add(wireframe);

camera.position.z = 5;


function render(){
	requestAnimationFrame(render);

	counter++;
	if(counter == 61){
		cube.material.color = 0xf00300;
		counter = 0;
	}
	cube.rotation.x += 0.02;
	cube.rotation.y += 0.01;

	renderer.render(scene, camera);
}

render();