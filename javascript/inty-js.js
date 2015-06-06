var scene = new THREE.Scene();

//PerspectiveCamera(fieldOfView, aspectRatio, clippingDistanceNear, clippingDistanceFar) Clipping distance will tell when to stop rendering visible objects
var camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);

var renderer = new THREE.WebGLRenderer();
renderer.setSize( window.innerWidth, window.innerHeight);

document.body.appendChild(renderer.domElement);

var geometry = new THREE.BoxGeometry(1, 1, 1);
var material = new THREE.MeshBasicMaterial({color: 0x00ff00, wireframe: true});
var cube = new THREE.Mesh(geometry, material);
scene.add(cube);

camera.position.z = 5;


function render(){
	requestAnimationFrame(render);

	cube.rotation.x += 0.02;
	cube.rotation.y += 0.01;

	renderer.render(scene, camera);
}

render();