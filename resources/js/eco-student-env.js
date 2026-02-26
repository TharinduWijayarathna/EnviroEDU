/**
 * EnviroEdu Student Dashboard – shared Three.js animated environment.
 * Mount to #eco-env-container; runs continuous animation (sky, ground, trees, clouds).
 */
import * as THREE from 'three';

(function () {
  const container = document.getElementById('eco-env-container');
  if (!container) return;

  const clock = { start: Date.now() };
  let scene, camera, renderer, treeGroup, cloudGroup, sunGroup;

  function init() {
    const width = container.clientWidth;
    const height = container.clientHeight;

    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xa8d8ea);
    scene.fog = new THREE.Fog(0xa8d8ea, 18, 55);

    camera = new THREE.PerspectiveCamera(50, width / height, 0.1, 100);
    camera.position.set(0, 6, 16);
    camera.lookAt(0, 0, 0);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    container.appendChild(renderer.domElement);

    // Ground
    const groundGeo = new THREE.PlaneGeometry(60, 60);
    const groundMat = new THREE.MeshStandardMaterial({ color: 0x5a9b4a });
    const ground = new THREE.Mesh(groundGeo, groundMat);
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    scene.add(ground);

    // Trees
    function makeTree() {
      const g = new THREE.Group();
      const trunk = new THREE.Mesh(
        new THREE.CylinderGeometry(0.35, 0.45, 2.2, 8),
        new THREE.MeshStandardMaterial({ color: 0x4e342e })
      );
      trunk.castShadow = true;
      trunk.position.y = 1.1;
      g.add(trunk);
      const leaves = new THREE.Mesh(
        new THREE.SphereGeometry(1.6, 12, 10),
        new THREE.MeshStandardMaterial({ color: 0x2e7d32 })
      );
      leaves.castShadow = true;
      leaves.position.y = 2.8;
      g.add(leaves);
      return g;
    }
    treeGroup = makeTree();
    treeGroup.position.set(-5, 0, -2);
    scene.add(treeGroup);
    const tree2 = makeTree().clone();
    tree2.position.set(6, 0, -4);
    tree2.scale.setScalar(0.9);
    scene.add(tree2);
    const tree3 = makeTree().clone();
    tree3.position.set(4, 0, 1);
    tree3.scale.setScalar(0.75);
    scene.add(tree3);
    const tree4 = makeTree().clone();
    tree4.position.set(-2, 0, 3);
    tree4.scale.setScalar(0.65);
    scene.add(tree4);

    // Clouds
    cloudGroup = new THREE.Group();
    [0, 1, 2, 3].forEach((i) => {
      const c = new THREE.Mesh(
        new THREE.SphereGeometry(1.2 + i * 0.25, 8, 6),
        new THREE.MeshBasicMaterial({ color: 0xffffff })
      );
      c.position.set(-4 + i * 3.5, 9 + i * 0.3, -6 - i * 1.5);
      cloudGroup.add(c);
    });
    scene.add(cloudGroup);

    // Sun (group for subtle motion)
    sunGroup = new THREE.Group();
    const sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(2.5, 16, 12),
      new THREE.MeshBasicMaterial({ color: 0xffeb3b })
    );
    sunMesh.position.set(12, 14, 8);
    sunGroup.add(sunMesh);
    scene.add(sunGroup);

    scene.add(new THREE.AmbientLight(0xffffff, 0.6));
    const dirLight = new THREE.DirectionalLight(0xffffff, 0.95);
    dirLight.position.set(12, 18, 10);
    dirLight.castShadow = true;
    dirLight.shadow.mapSize.set(1024, 1024);
    scene.add(dirLight);
  }

  function animate() {
    requestAnimationFrame(animate);
    const t = (Date.now() - clock.start) * 0.001;
    if (treeGroup) treeGroup.rotation.y = Math.sin(t * 0.35) * 0.06;
    if (cloudGroup) {
      cloudGroup.position.x = Math.sin(t * 0.12) * 2;
      cloudGroup.position.z = Math.cos(t * 0.08) * 1;
    }
    if (sunGroup) sunGroup.rotation.y = t * 0.02;
    renderer.render(scene, camera);
  }

  function onResize() {
    const w = container.clientWidth;
    const h = container.clientHeight;
    camera.aspect = w / h;
    camera.updateProjectionMatrix();
    renderer.setSize(w, h);
  }

  init();
  animate();
  window.addEventListener('resize', onResize);
})();
