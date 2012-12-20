<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Campus Conquest Login</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<LINK REL=StyleSheet HREF="css/login.css" TYPE="text/css">
	</head>

	<body>
		<div id = "container">

		</div>
		<div  id = "logo">
			<img src="rsc/images/logo_small.png" style="z-index : 1000;">
		</div>
		<div id="main" >
			<div id="login_title">
				Login:
			</div>

			<br>
			<form id="form">
				NetID:
				<br>
				<input type="text" name="id" class = "field">
				<br>
				<br>
				Password:
				<br>
				<input type="password" name="pw" class = "field">
				<br>
				<br>
				<br>
				<br>
				<br>


				<input name="submit" type="image" src="rsc/images/buttons/continue.png" onclick="verifyId($("form").serialize())" style="position: absolute;
				right: 1%; bottom: 20px;">
			</form>

		</div>

		<div id="version">
			1.2 β
		</div>

		<div id="lower_left_options">
			<a href="#">New User?</a>
			<br/>
			Options
		</div>

		<div id="copyright">
			&copy; 2013 Campus Conquest
		</div>

		<script src="js/lib/Three.js"></script>

		<script src="js/lib/shaders/ConvolutionShader.js"></script>
		<script src="js/lib/shaders/CopyShader.js"></script>

		<script src="js/lib/postprocessing/EffectComposer.js"></script>
		<script src="js/lib/postprocessing/MaskPass.js"></script>
		<script src="js/lib/postprocessing/RenderPass.js"></script>
		<script src="js/lib/postprocessing/ShaderPass.js"></script>
		<script src="js/lib/postprocessing/BloomPass.js"></script>

		<script src="js/lib/Detector.js"></script>
		<script src="js/lib/jQuery.js"></script>

		<script>
			function verifyId(data) {
				alert(data.id);
				$.get("1.html", function(response) {
					alert(response);
					//do you operations
				});
			}
		</script>

		<script>
			$("#main").center();

			if (!Detector.webgl)
				Detector.addGetWebGLMessage();

			var container, loader;

			var camera, scene, renderer;

			var mesh, zmesh, lightMesh, geometry;
			var mesh1;

			var directionalLight, pointLight, ambientLight;

			var mouseX = 0;
			var mouseY = 0;

			var postprocessing = {
				enabled : true
			};

			var composer;

			SCREEN_WIDTH = window.innerWidth;
			SCREEN_HEIGHT = window.innerHeight;

			var windowHalfX = SCREEN_WIDTH / 2;
			var windowHalfY = SCREEN_HEIGHT / 2;

			init();
			animate();

			//

			function init() {

				container = document.getElementById("container");

				document.body.appendChild(container);

				camera = new THREE.PerspectiveCamera(20, SCREEN_WIDTH / SCREEN_HEIGHT, 1, 10000);
				camera.position.z = 1200;

				scene = new THREE.Scene();

				// LIGHTS

				// dl = new THREE.DirectionalLight(0xffffff, 5, 1000);
				// dl.position.set(camera.x,camera.y,camera.z);
				// scene.add(dl);

				// directionalLight = new THREE.DirectionalLight(0xffffff);
				// // directionalLight.position.set(1, -0.5, -1);
				// directionalLight.position.set(-350, 250, -1000);
				//
				// scene.add(directionalLight);

				// lens flares

				var textureFlare0 = THREE.ImageUtils.loadTexture("rsc/textures/lensflare/lensflare0.png");
				var textureFlare2 = THREE.ImageUtils.loadTexture("rsc/textures/lensflare/lensflare2.png");
				var textureFlare3 = THREE.ImageUtils.loadTexture("rsc/textures/lensflare/lensflare3.png");

				//addLight(0.55, 0.825, 0.99, 5000, 0, -1000);
				addLight(0, 0, .5, -500, 250, -1000);
				//addLight(0.995, 0.025, 0.99, 5000, 5000, -1000);

				function addLight(h, s, v, x, y, z) {

					var light = new THREE.PointLight(0xffffff, 5, 4500);
					light.position.set(x, y, z);
					scene.add(light);

					light.color.setHSV(h, s, v);

					var flareColor = new THREE.Color(0xffffff);
					flareColor.copy(light.color);
					THREE.ColorUtils.adjustHSV(flareColor, 0, -0.5, 0.5);

					var lensFlare = new THREE.LensFlare(textureFlare0, 700, 0.0, THREE.AdditiveBlending, flareColor);

					lensFlare.add(textureFlare2, 512, 0.0, THREE.AdditiveBlending);
					lensFlare.add(textureFlare2, 512, 0.0, THREE.AdditiveBlending);
					lensFlare.add(textureFlare2, 512, 0.0, THREE.AdditiveBlending);

					lensFlare.add(textureFlare3, 60, 0.4, THREE.AdditiveBlending);
					lensFlare.add(textureFlare3, 70, 0.5, THREE.AdditiveBlending);
					lensFlare.add(textureFlare3, 120, 0.7, THREE.AdditiveBlending);
					lensFlare.add(textureFlare3, 70, .8, THREE.AdditiveBlending);

					lensFlare.customUpdateCallback = lensFlareUpdateCallback;
					lensFlare.position = light.position;

					scene.add(lensFlare);

				}

				// material parameters

				var ambient = 0x888, diffuse = 0xbbbbbb, specular = 0x060606, shininess = 20;

				var shader = THREE.ShaderUtils.lib["normal"];
				var uniforms = THREE.UniformsUtils.clone(shader.uniforms);

				uniforms["tNormal"].value = THREE.ImageUtils.loadTexture("rsc/obj/aaa_scenes/mcgraw/mcgraw_nrm_inverted.png");
				uniforms["uNormalScale"].value.set(0.8, 0.8);

				uniforms["tDiffuse"].value = THREE.ImageUtils.loadTexture("rsc/obj/aaa_scenes/mcgraw/mcgraw_inverted.png");

				uniforms["enableAO"].value = false;
				uniforms["enableDiffuse"].value = true;
				uniforms["enableSpecular"].value = true;

				uniforms["uDiffuseColor"].value.setHex(diffuse);
				uniforms["uSpecularColor"].value.setHex(specular);
				uniforms["uAmbientColor"].value.setHex(ambient);

				uniforms["uShininess"].value = shininess;

				uniforms["wrapRGB"].value.set(0.575, 0.5, 0.5);

				var parameters = {
					fragmentShader : shader.fragmentShader,
					vertexShader : shader.vertexShader,
					uniforms : uniforms,
					lights : true
				};
				var material = new THREE.ShaderMaterial(parameters);

				material.wrapAround = true;

				loader = new THREE.JSONLoader(true);
				document.body.appendChild(loader.statusDomElement);

				loader.load(" rsc/obj/aaa_scenes/mcgraw/mcgrawHiRes.js", function(geometry) {
					createScene(geometry, 100, material)
				});

				renderer = new THREE.WebGLRenderer({
					antialias : true,
					clearColor : 0x508ab7,
					clearAlpha : 1
				});
				renderer.setSize(window.innerWidth, window.innerHeight);
				container.appendChild(renderer.domElement);

				//

				renderer.autoClear = false;
				//renderer.setClearColor( 0x0791c4, 1 );

				//

				renderer.gammaInput = true;
				renderer.gammaOutput = true;
				renderer.physicallyBasedShading = true;

				// Post Processing

				var renderModel = new THREE.RenderPass(scene, camera);
				var effectBloom = new THREE.BloomPass(.5, 20, 4, 256);
				//strength, kernelSize, sigma, resolution
				var effectScreen = new THREE.ShaderPass(THREE.CopyShader);
				effectScreen.renderToScreen = true;
				composer = new THREE.EffectComposer(renderer);
				composer.addPass(renderModel);
				composer.addPass(effectBloom);
				composer.addPass(effectScreen);

				// EVENTS

				document.addEventListener('mousemove', onDocumentMouseMove, false);
				window.addEventListener('resize', onWindowResize, false);

			}

			//

			function lensFlareUpdateCallback(object) {

				var f, fl = object.lensFlares.length;
				var flare;
				var vecX = -object.positionScreen.x * 2;
				var vecY = -object.positionScreen.y * 2;

				for ( f = 0; f < fl; f++) {

					flare = object.lensFlares[f];

					flare.x = object.positionScreen.x + vecX * flare.distance;
					flare.y = object.positionScreen.y + vecY * flare.distance;

					flare.rotation = 0;

				}

				object.lensFlares[2].y += 0.025;
				object.lensFlares[3].rotation = object.positionScreen.x * 0.5 + 45 * Math.PI / 180;

			}

			//

			function createScene(geometry, scale, material) {

				geometry.computeTangents();

				mesh1 = new THREE.Mesh(geometry, material);

				mesh1.position.y = -500;
				mesh1.position.x = 200;
				mesh1.position.z = 100;
				mesh1.scale.x = mesh1.scale.y = mesh1.scale.z = scale;

				scene.add(mesh1);

				loader.statusDomElement.style.display = "none";

			}

			//

			function onWindowResize(event) {

				renderer.setSize(SCREEN_WIDTH, SCREEN_HEIGHT);

				camera.aspect = SCREEN_WIDTH / SCREEN_HEIGHT;
				camera.updateProjectionMatrix();

				if (postprocessing.enabled)
					composer.reset();

				//effectFXAA.uniforms['resolution'].value.set(1 / SCREEN_WIDTH, 1 / SCREEN_HEIGHT);

			}

			function onDocumentMouseMove(event) {

				mouseX = (event.clientX - windowHalfX );
				mouseY = (event.clientY - windowHalfY ) * (-1);

			}

			//

			function animate() {

				requestAnimationFrame(animate);

				render();

			}

			function render() {

				var ry = mouseX * 0.0003, rx = mouseY * 0.0003;

				if (mesh1) {

					mesh1.rotation.y = ry - .6;
					//mesh1.rotation.x = rx;

				}

				camera.rotation.y = -.1 * ry;
				camera.rotation.x = .1 * rx;

				renderer.render(scene, camera);

				if (postprocessing.enabled) {
					renderer.clear();
					composer.render(0.1);
				} else {
					renderer.render(scene, camera);
				}

			}

		</script>

	</body>
</html>
