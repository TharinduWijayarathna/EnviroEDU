import * as THREE from 'three';

let scene;
let camera;
let renderer;
let earth;
let clouds;
let animationId;

function createFloatingSprites(parentScene) {
    const elements = ['🌱', '🌿', '💧', '🌍', '♻️', '🌳', '🦋'];
    const sprites = [];

    for (let i = 0; i < 12; i++) {
        const canvas = document.createElement('canvas');
        canvas.width = 128;
        canvas.height = 128;
        const ctx = canvas.getContext('2d');
        ctx.font = '80px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(elements[i % elements.length], 64, 64);

        const texture = new THREE.CanvasTexture(canvas);
        texture.needsUpdate = true;
        const material = new THREE.SpriteMaterial({ map: texture, transparent: true });
        const sprite = new THREE.Sprite(material);

        sprite.position.set(
            (Math.random() - 0.5) * 14,
            (Math.random() - 0.5) * 10,
            (Math.random() - 0.5) * 10 - 3
        );
        sprite.scale.set(0.4, 0.4, 1);
        parentScene.add(sprite);
        sprites.push({ sprite, speed: 0.2 + Math.random() * 0.3 });
    }

    return sprites;
}

function animateScene() {
    animationId = requestAnimationFrame(animateScene);

    if (earth) {
        earth.rotation.y += 0.002;
    }
    if (clouds) {
        clouds.rotation.y += 0.003;
    }

    if (renderer && scene && camera) {
        renderer.render(scene, camera);
    }
}

function onResize(container) {
    if (!camera || !renderer || !container) return;
    const width = container.clientWidth;
    const height = container.clientHeight;
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height);
}

export function initEcoScene(containerId = 'canvas-container') {
    const container = document.getElementById(containerId);
    if (!container) return null;

    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x667eea);

    camera = new THREE.PerspectiveCamera(
        75,
        container.clientWidth / container.clientHeight,
        0.1,
        1000
    );
    camera.position.z = 5;

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    container.appendChild(renderer.domElement);

    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(5, 3, 5);
    scene.add(directionalLight);

    const earthGeometry = new THREE.SphereGeometry(1.5, 32, 32);
    const earthMaterial = new THREE.MeshPhongMaterial({
        color: 0x4ecdc4,
        emissive: 0x112244,
        shininess: 20,
    });
    earth = new THREE.Mesh(earthGeometry, earthMaterial);
    earth.position.set(-3, 1, 0);
    scene.add(earth);

    const cloudGeometry = new THREE.SphereGeometry(1.55, 32, 32);
    const cloudMaterial = new THREE.MeshPhongMaterial({
        color: 0xffffff,
        transparent: true,
        opacity: 0.4,
    });
    clouds = new THREE.Mesh(cloudGeometry, cloudMaterial);
    clouds.position.set(-3, 1, 0);
    scene.add(clouds);

    createFloatingSprites(scene);

    const resizeObserver = new ResizeObserver(() => onResize(container));
    resizeObserver.observe(container);
    window.addEventListener('resize', () => onResize(container));

    animateScene();

    const resizeHandler = () => onResize(container);

    return {
        stop() {
            if (animationId) cancelAnimationFrame(animationId);
            resizeObserver.disconnect();
            window.removeEventListener('resize', resizeHandler);
            if (renderer.domElement && renderer.domElement.parentNode) {
                renderer.domElement.parentNode.removeChild(renderer.domElement);
            }
            renderer.dispose();
        },
    };
}

function autoInit() {
    const container = document.getElementById('canvas-container') || document.getElementById('eco-canvas');
    if (container) {
        initEcoScene(container.id);
    }
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', autoInit);
    } else {
        autoInit();
    }
}
