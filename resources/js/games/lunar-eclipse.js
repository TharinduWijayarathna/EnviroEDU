/**
 * How a Lunar Eclipse Works – Earth blocks sunlight from the Moon.
 * Sun → Earth → Moon in line; Earth's shadow falls on the Moon.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, sunMesh, earthMesh, moonMesh, sunLight;
  let shadowSphere;
  let orbitAngle = 0;
  let eclipsePhase = 0;

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a0a1a);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 4, 14);
    camera.lookAt(0, 0, 2);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0x222244, 0.25));

    sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(1.4, 32, 32),
      new THREE.MeshBasicMaterial({ color: 0xffdd44 })
    );
    sunMesh.position.set(-8, 0, 2);
    sunLight = new THREE.DirectionalLight(0xffffff, 1.2);
    sunLight.position.copy(sunMesh.position);
    sunLight.target.position.set(0, 0, 2);
    scene.add(sunLight.target);
    scene.add(sunLight);
    scene.add(sunMesh);

    earthMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.9, 48, 48),
      new THREE.MeshStandardMaterial({
        color: 0x1565c0,
        roughness: 0.9,
        metalness: 0.05,
      })
    );
    earthMesh.position.set(0, 0, 2);
    scene.add(earthMesh);

    moonMesh = new THREE.Mesh(
      new THREE.SphereGeometry(0.5, 32, 32),
      new THREE.MeshStandardMaterial({
        color: 0xe0e0e0,
        roughness: 0.9,
        metalness: 0.05,
      })
    );
    moonMesh.position.set(4, 0, 2);
    scene.add(moonMesh);

    const shadowGeo = new THREE.CircleGeometry(0.48, 32);
    shadowSphere = new THREE.Mesh(
      shadowGeo,
      new THREE.MeshBasicMaterial({
        color: 0x0d0d1a,
        side: THREE.DoubleSide,
      })
    );
    shadowSphere.position.copy(moonMesh.position);
    shadowSphere.visible = false;
    scene.add(shadowSphere);

    const label = document.createElement('div');
    label.style.cssText =
      'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.85);color:#fff;padding:12px 24px;border-radius:16px;font-weight:700;z-index:10;text-align:center;max-width:92%;';
    label.textContent = '🌒 Lunar eclipse: When Earth is between Sun and Moon, Earth\'s shadow covers the Moon.';
    mount.style.position = 'relative';
    mount.appendChild(label);

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText =
      'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#37474f;color:#fff;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('🌒', 'Great Job!', 'You learned how a lunar eclipse works!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    orbitAngle += 0.008;
    eclipsePhase += 0.006;

    const moonOrbitRadius = 4;
    moonMesh.position.x = Math.cos(orbitAngle) * moonOrbitRadius;
    moonMesh.position.z = 2 + Math.sin(orbitAngle) * moonOrbitRadius;

    const sunToEarth = new THREE.Vector3().subVectors(earthMesh.position, sunMesh.position).normalize();
    const earthToMoon = new THREE.Vector3().subVectors(moonMesh.position, earthMesh.position).normalize();
    const alignment = sunToEarth.dot(earthToMoon);
    const inEclipse = alignment > 0.85;

    if (shadowSphere) {
      shadowSphere.position.copy(moonMesh.position);
      shadowSphere.visible = inEclipse;
      const dir = new THREE.Vector3().subVectors(earthMesh.position, moonMesh.position).normalize();
      shadowSphere.position.addScaledVector(dir, 0.26);
      shadowSphere.lookAt(earthMesh.position);
    }

    renderer.render(scene, camera);
  }

  init();
})();
