/**
 * How Day and Night Work – Earth rotates; one side faces Sun (day), other side is dark (night).
 * User can drag to rotate the Earth. Earth orbits the Sun and spins on its axis.
 */
import * as THREE from 'three';
import { recordComplete, showWinUI } from './platform-game-utils.js';

(function () {
  const mount = document.getElementById('platform-game-mount');
  if (!mount || !window.EnviroEduPlatformGame) return;
  const { slug, progressUrl, csrfToken } = window.EnviroEduPlatformGame;

  let scene, camera, renderer, sunMesh, earthGroup, earthMesh, sunLight;
  let orbitAngle = 0;
  let earthSpin = 0;
  let isDragging = false;
  let prevMouseX = 0;

  function createEarthTexture() {
    const size = 256;
    const canvas = document.createElement('canvas');
    canvas.width = size;
    canvas.height = size;
    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, size, 0);
    gradient.addColorStop(0, '#1a5276');
    gradient.addColorStop(0.2, '#2874a6');
    gradient.addColorStop(0.4, '#2e86ab');
    gradient.addColorStop(0.5, '#148f77');
    gradient.addColorStop(0.65, '#1e8449');
    gradient.addColorStop(0.8, '#229954');
    gradient.addColorStop(1, '#1a5276');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, size, size);
    ctx.fillStyle = 'rgba(30, 132, 73, 0.6)';
    ctx.beginPath();
    ctx.ellipse(size * 0.3, size * 0.4, size * 0.25, size * 0.2, 0.2, 0, Math.PI * 2);
    ctx.fill();
    ctx.beginPath();
    ctx.ellipse(size * 0.7, size * 0.55, size * 0.2, size * 0.18, -0.1, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = 'rgba(139, 90, 43, 0.5)';
    ctx.beginPath();
    ctx.ellipse(size * 0.55, size * 0.25, size * 0.22, size * 0.15, 0, 0, Math.PI * 2);
    ctx.fill();
    const tex = new THREE.CanvasTexture(canvas);
    tex.wrapS = THREE.RepeatWrapping;
    tex.wrapT = THREE.ClampToEdgeWrapping;
    return tex;
  }

  function init() {
    const w = mount.getBoundingClientRect().width || 800;
    const h = mount.getBoundingClientRect().height || 520;
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0a0a1a);
    camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
    camera.position.set(0, 3, 10);
    camera.lookAt(0, 0, 0);
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    mount.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0x111122, 0.15));

    const sunRadius = 1.2;
    sunMesh = new THREE.Mesh(
      new THREE.SphereGeometry(sunRadius, 32, 32),
      new THREE.MeshBasicMaterial({ color: 0xffdd44 })
    );
    sunMesh.position.set(5, 0, 0);
    sunLight = new THREE.DirectionalLight(0xffeedd, 1.4);
    sunLight.position.copy(sunMesh.position);
    sunLight.target.position.set(0, 0, 0);
    scene.add(sunLight.target);
    scene.add(sunLight);
    scene.add(sunMesh);

    earthGroup = new THREE.Group();
    const earthGeo = new THREE.SphereGeometry(0.7, 48, 48);
    earthMesh = new THREE.Mesh(
      earthGeo,
      new THREE.MeshStandardMaterial({
        map: createEarthTexture(),
        roughness: 0.85,
        metalness: 0.05,
      })
    );
    earthGroup.add(earthMesh);
    scene.add(earthGroup);

    const label = document.createElement('div');
    label.style.cssText =
      'position:absolute;top:12px;left:50%;transform:translateX(-50%);background:rgba(0,0,0,0.75);color:#fff;padding:12px 24px;border-radius:16px;font-weight:700;z-index:10;text-align:center;max-width:90%;';
    label.textContent = '🌍 Drag to rotate Earth. Bright side = day (sunrise/sunset); dark side = night.';
    mount.style.position = 'relative';
    mount.style.cursor = 'grab';
    mount.appendChild(label);

    mount.addEventListener('mousedown', (e) => {
      if (e.button === 0) {
        isDragging = true;
        prevMouseX = e.clientX;
      }
    });
    mount.addEventListener('mousemove', (e) => {
      if (isDragging) {
        const delta = (e.clientX - prevMouseX) * 0.01;
        earthSpin -= delta;
        prevMouseX = e.clientX;
      }
    });
    mount.addEventListener('mouseup', () => { isDragging = false; });
    mount.addEventListener('mouseleave', () => { isDragging = false; });

    const btn = document.createElement('button');
    btn.textContent = "✓ I've learned!";
    btn.style.cssText =
      'position:absolute;bottom:20px;left:50%;transform:translateX(-50%);padding:14px 28px;border-radius:20px;font-weight:700;background:#2196f3;color:#fff;border:none;cursor:pointer;z-index:10;';
    btn.onclick = () => {
      showWinUI('🌍', 'Great Job!', 'You learned why we have day and night!');
      recordComplete(slug, progressUrl, csrfToken, {});
    };
    mount.appendChild(btn);

    animate();
  }

  function animate() {
    requestAnimationFrame(animate);
    const dt = 0.012;

    orbitAngle += dt * 0.15;
    const orbitRadius = 3.5;
    earthGroup.position.x = Math.cos(orbitAngle) * orbitRadius;
    earthGroup.position.z = Math.sin(orbitAngle) * orbitRadius;

    earthSpin += dt * 0.4;
    earthMesh.rotation.y = earthSpin;

    sunLight.position.copy(sunMesh.position);
    sunLight.target.position.copy(earthGroup.position);

    renderer.render(scene, camera);
  }

  init();
})();
