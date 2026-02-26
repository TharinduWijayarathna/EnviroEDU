/**
 * How a Rainbow is Made – sun, rain, and light refraction.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, sunMesh, rainbowGroup;

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x87ceeb);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 0, 8);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 0.7));
    const dir = new THREE.DirectionalLight(0xffffff, 0.9);
    dir.position.set(5, 10, 5);
    scene.add(dir);

    const ground = new THREE.Mesh(
      new THREE.PlaneGeometry(30, 30),
      new THREE.MeshStandardMaterial({ color: 0x689f38 })
    );
    ground.rotation.x = -Math.PI / 2;
    scene.add(ground);

    sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.6, 24, 24),
      new THREE.MeshBasicMaterial({ color: 0xffeb3b })
    );
    sunMesh.position.set(-3, 2.5, 2);
    scene.add(sunMesh);

    const colors = [0xff0000, 0xff7f00, 0xffff00, 0x00ff00, 0x0000ff, 0x4b0082, 0x8f00ff];
    rainbowGroup = new THREE.Group();
    colors.forEach((color, i) => {
      const arc = new THREE.Mesh(
        new THREE.TorusGeometry(3 - i * 0.15, 0.12, 8, 32, Math.PI),
        new THREE.MeshBasicMaterial({ color, side: THREE.DoubleSide })
      );
      arc.rotation.x = Math.PI / 2;
      arc.rotation.z = -0.2;
      arc.position.set(1, 0, -1);
      rainbowGroup.add(arc);
    });
    scene.add(rainbowGroup);

    const label = document.createElement('div');
    label.style.cssText = 'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(255,255,255,0.95);padding:12px 24px;border-radius:16px;font-weight:700;color:#1a3c34;z-index:10;text-align:center;';
    label.textContent = '🌈 Sunlight + rain = rainbow! Light bends in water drops.';
    mount.style.position = 'relative';
    mount.appendChild(label);

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText = 'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#4caf50;color:#fff;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('🌈', 'Great Job!', 'You learned how rainbows are made from light and water!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    if (sunMesh) sunMesh.rotation.y += 0.01;
    if (rainbowGroup) rainbowGroup.rotation.y += 0.002;
    renderer.render(scene, camera);
  }

  init();
})();
