/**
 * Star Patterns – connect dots to see constellation patterns.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  const constellations = [
    { name: 'Big Dipper', points: [[0, 2], [1, 1.8], [2, 1.5], [3, 1.2], [2.5, 0.8], [1.5, 0.6], [0.5, 1]] },
    { name: 'Orion', points: [[-1, 1], [0, 1.5], [1, 1], [0, 0.5], [0, 0]] },
  ];
  let scene, camera, renderer, starsGroup;

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a0a1a);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 0, 8);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 0.3));
    const light = new THREE.PointLight(0xffffff, 0.8);
    light.position.set(0, 0, 5);
    scene.add(light);

    starsGroup = new THREE.Group();
    constellations.forEach((c, ci) => {
      c.points.forEach((p, i) => {
        const star = new THREE.Mesh(
          new THREE.SphereGeometry(0.12, 12, 12),
          new THREE.MeshBasicMaterial({ color: 0xffffaa })
        );
        star.position.set(p[0] * 1.2 - 1.5, p[1] * 1.2 - 1, -ci * 0.5);
        starsGroup.add(star);
      });
      const linePoints = c.points.map((p) => new THREE.Vector3(p[0] * 1.2 - 1.5, p[1] * 1.2 - 1, -ci * 0.5));
      const lineGeo = new THREE.BufferGeometry().setFromPoints(linePoints);
      const line = new THREE.Line(lineGeo, new THREE.LineBasicMaterial({ color: 0x4488ff, transparent: true, opacity: 0.6 }));
      starsGroup.add(line);
    });
    scene.add(starsGroup);

    const label = document.createElement('div');
    label.style.cssText = 'position:absolute;top:16px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.7);color:#fff;padding:10px 20px;border-radius:12px;font-weight:700;z-index:10;';
    label.textContent = '⭐ Star patterns – people connect stars to see shapes in the sky';
    mount.style.position = 'relative';
    mount.appendChild(label);

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText = 'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#ffc107;color:#1a1a1a;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('⭐', 'Great Job!', 'You learned about star patterns in the sky!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    if (starsGroup) starsGroup.rotation.y += 0.002;
    renderer.render(scene, camera);
  }

  init();
})();
