/**
 * How Day and Night Work – Earth rotates, one side faces Sun.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, sunMesh, earthGroup;
  let autoRotate = true;

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a0a1a);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 2, 8);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0x111122, 0.5));

    sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.8, 24, 24),
      new THREE.MeshBasicMaterial({ color: 0xffeb3b })
    );
    sunMesh.position.set(4, 0, 0);
    const sunLight = new THREE.PointLight(0xffeedd, 1.5, 20);
    sunLight.position.copy(sunMesh.position);
    scene.add(sunLight);
    scene.add(sunMesh);

    earthGroup = new THREE.Group();
    const earth = new THREE.Mesh(
      new THREE.SphereGeometry(0.5, 32, 32),
      new THREE.MeshStandardMaterial({
        color: 0x2196f3,
        roughness: 0.9,
        metalness: 0.1,
      })
    );
    earthGroup.add(earth);
    earthGroup.position.set(-1.5, 0, 0);
    scene.add(earthGroup);

    const label = document.createElement('div');
    label.style.cssText = 'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.7);color:#fff;padding:12px 24px;border-radius:16px;font-weight:700;z-index:10;text-align:center;';
    label.textContent = '🌍 Earth spins → one side gets sun (day), the other is dark (night)';
    mount.style.position = 'relative';
    mount.appendChild(label);

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText = 'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#2196f3;color:#fff;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('🌍', 'Great Job!', 'You learned why we have day and night!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    if (autoRotate && earthGroup) {
      earthGroup.rotation.y += 0.008;
    }
    renderer.render(scene, camera);
  }

  init();
})();
