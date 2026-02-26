/**
 * EnviroEdu Environmental 3D Games – Three.js animated scene + drag-and-drop or matching pairs.
 */
import * as THREE from 'three';

(function () {
  const mount = document.getElementById('game-mount');
  if (!mount || !window.EnviroEduGame) return;

  const { config, gameId, progressGameUrl, csrfToken } = window.EnviroEduGame;
  const gameType = config?.game_type || 'drag_drop';
  const categories = config?.categories || [];
  const items = config?.items || [];
  const pairs = config?.pairs || [];
  const questions = config?.questions || [];

  function recordGameComplete() {
    if (!gameId || !progressGameUrl || !csrfToken) return;
    fetch(progressGameUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
      body: JSON.stringify({ mini_game_id: gameId, completed: true }),
    })
      .then((r) => r.json())
      .then((data) => {
        if (data.new_badges && data.new_badges.length > 0 && window.ecoShowBadgeModal) {
          data.new_badges.forEach((b) => window.ecoShowBadgeModal(b));
        }
      })
      .catch(() => {});
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
  }

  const EMOJI_POOL = ['🌱', '♻️', '🐝', '🌍', '🍃', '💧', '🌻', '🐸', '🌲', '🌈', '☀️', '🌊', '🦋', '🐻', '🥕', '🌳', '🍎', '🧴', '📦', '⚡'];
  /** Ensure label has a leading emoji for kids; use varied default by index if missing. */
  function withEmoji(label, defaultEmojiOrIndex = '🌍') {
    const s = String(label || '').trim();
    if (!s) return typeof defaultEmojiOrIndex === 'number' ? EMOJI_POOL[defaultEmojiOrIndex % EMOJI_POOL.length] : defaultEmojiOrIndex;
    const firstCode = s.codePointAt(0);
    const isLikelyEmoji = firstCode > 0x1f300 || (firstCode >= 0x2600 && firstCode <= 0x27bf);
    if (isLikelyEmoji) return s;
    const emoji = typeof defaultEmojiOrIndex === 'number' ? EMOJI_POOL[defaultEmojiOrIndex % EMOJI_POOL.length] : defaultEmojiOrIndex;
    return emoji + ' ' + s;
  }

  // —— Three.js animated scene ——
  let scene, camera, renderer, treeGroup, cloudGroup;
  const clock = { start: Date.now() };

  function initThree(container) {
    const { width, height } = container.getBoundingClientRect();
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xa8d8ea);
    scene.fog = new THREE.Fog(0xa8d8ea, 20, 55);

    camera = new THREE.PerspectiveCamera(52, width / height, 0.1, 100);
    camera.position.set(0, 5, 14);
    camera.lookAt(0, 0, 0);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    container.appendChild(renderer.domElement);

    // Ground
    const groundGeo = new THREE.PlaneGeometry(50, 50);
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
    treeGroup.position.set(-4, 0, 0);
    scene.add(treeGroup);
    const tree2 = makeTree().clone();
    tree2.position.set(5, 0, -3);
    tree2.scale.setScalar(0.85);
    scene.add(tree2);
    const tree3 = makeTree().clone();
    tree3.position.set(3, 0, 2);
    tree3.scale.setScalar(0.7);
    scene.add(tree3);

    // Simple clouds (spheres)
    cloudGroup = new THREE.Group();
    [0, 1, 2].forEach((i) => {
      const c = new THREE.Mesh(
        new THREE.SphereGeometry(1.2 + i * 0.3, 8, 6),
        new THREE.MeshBasicMaterial({ color: 0xffffff })
      );
      c.position.set(-3 + i * 4, 8 + i * 0.5, -5 - i * 2);
      cloudGroup.add(c);
    });
    scene.add(cloudGroup);

    scene.add(new THREE.AmbientLight(0xffffff, 0.65));
    const sun = new THREE.DirectionalLight(0xffffff, 0.95);
    sun.position.set(12, 18, 10);
    sun.castShadow = true;
    sun.shadow.mapSize.set(1024, 1024);
    scene.add(sun);

    function animate() {
      requestAnimationFrame(animate);
      const t = (Date.now() - clock.start) * 0.001;
      if (treeGroup) treeGroup.rotation.y = Math.sin(t * 0.4) * 0.08;
      if (cloudGroup) cloudGroup.position.x = Math.sin(t * 0.15) * 1.5;
      renderer.render(scene, camera);
    }
    animate();

    window.addEventListener('resize', () => {
      const w = container.clientWidth;
      const h = container.clientHeight;
      camera.aspect = w / h;
      camera.updateProjectionMatrix();
      renderer.setSize(w, h);
    });
  }

  // —— Shared: mount + overlay + 3D container ——
  mount.style.position = 'relative';
  mount.style.minHeight = '400px';

  const container = document.createElement('div');
  container.id = 'eco-3d-container';
  container.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;min-height:360px;';
  mount.appendChild(container);

  const overlay = document.createElement('div');
  overlay.className = 'eco-3d-overlay';
  overlay.style.cssText = 'position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;padding:1rem;pointer-events:none;box-sizing:border-box;';
  overlay.style.pointerEvents = 'auto';

  const overlayInner = document.createElement('div');
  overlayInner.className = 'eco-3d-game-ui';
  overlayInner.style.cssText = 'width:100%;max-width:720px;pointer-events:auto;';
  overlay.appendChild(overlayInner);

  initThree(container);
  mount.appendChild(overlay);

  // —— Drag & Drop game ——
  function runDragDrop() {
    const shuffled = [...items].sort(() => Math.random() - 0.5);
    let correctCount = 0;

    let html = '<p class="eco-3d-instruction">Drag each item into the correct category.</p>';
    html += '<div class="eco-3d-zones">';
    categories.forEach((cat, idx) => {
      html += `<div class="eco-3d-drop-zone" data-category="${escapeHtml(cat.id)}"><span class="eco-3d-zone-label">${escapeHtml(withEmoji(cat.label, idx))}</span><div class="eco-3d-dropped"></div></div>`;
    });
    html += '</div>';
    html += '<div class="eco-3d-draggable-pool">';
    shuffled.forEach((item, i) => {
      html += `<div class="eco-3d-draggable" draggable="true" data-category="${escapeHtml(item.category_id)}" data-index="${i}">${escapeHtml(withEmoji(item.label, i))}</div>`;
    });
    html += '</div>';

    overlayInner.innerHTML = html;

    const pool = overlayInner.querySelector('.eco-3d-draggable-pool');
    const zones = overlayInner.querySelectorAll('.eco-3d-drop-zone');

    overlayInner.querySelectorAll('.eco-3d-draggable').forEach((el) => {
      el.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', el.dataset.category);
        e.dataTransfer.setData('index', el.dataset.index);
        el.classList.add('eco-3d-dragging');
      });
      el.addEventListener('dragend', () => el.classList.remove('eco-3d-dragging'));
    });

    zones.forEach((zone) => {
      zone.addEventListener('dragover', (e) => {
        e.preventDefault();
        zone.classList.add('eco-3d-drag-over');
      });
      zone.addEventListener('dragleave', () => zone.classList.remove('eco-3d-drag-over'));
      zone.addEventListener('drop', (e) => {
        e.preventDefault();
        zone.classList.remove('eco-3d-drag-over');
        const category = e.dataTransfer.getData('text/plain');
        const index = e.dataTransfer.getData('index');
        if (zone.dataset.category !== category) {
          const item = pool.querySelector(`[data-index="${index}"]`);
          if (item) {
            item.classList.add('eco-3d-wrong');
            setTimeout(() => item.classList.remove('eco-3d-wrong'), 600);
          }
          return;
        }
        const item = pool.querySelector(`[data-index="${index}"]`);
        if (!item) return;
        item.draggable = false;
        item.classList.add('eco-3d-correct');
        zone.querySelector('.eco-3d-dropped').appendChild(item);
        correctCount++;
        if (correctCount === items.length) {
          overlayInner.innerHTML = '<div class="eco-3d-result"><h2>Well done!</h2><p>All sorted correctly.</p></div>';
          recordGameComplete();
        }
      });
    });
  }

  // —— Matching pairs game (draw arrow from left to right) ——
  function runMatching() {
    const leftItems = pairs.map((p, i) => ({ id: i, text: p.left })).sort(() => Math.random() - 0.5);
    const rightItems = pairs.map((p, i) => ({ id: i, text: p.right })).sort(() => Math.random() - 0.5);
    let matchedCount = 0;
    let dragLine = null;
    let dragFromCard = null;
    let dragFromPos = null;

    let html = '<p class="eco-3d-instruction">Draw a line from each item on the left to the matching one on the right.</p>';
    html += '<div class="eco-3d-matching-wrap">';
    html += '<svg class="eco-3d-match-lines" aria-hidden="true"><defs><marker id="eco-arrowhead" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto"><polygon points="0 0, 10 4, 0 8" fill="var(--eco-primary, #4ecdc4)"/></marker><marker id="eco-arrowhead-done" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto"><polygon points="0 0, 10 4, 0 8" fill="#2e7d32"/></marker><marker id="eco-arrowhead-wrong" markerWidth="10" markerHeight="8" refX="9" refY="4" orient="auto"><polygon points="0 0, 10 4, 0 8" fill="#e57373"/></marker></defs></svg>';
    html += '<div class="eco-3d-matching-grid">';
    html += '<div class="eco-3d-match-col eco-3d-match-left">';
    leftItems.forEach((item, idx) => {
      html += `<div class="eco-3d-match-card" data-id="${item.id}" data-side="left">${escapeHtml(withEmoji(item.text, idx))}</div>`;
    });
    html += '</div><div class="eco-3d-match-col eco-3d-match-right">';
    rightItems.forEach((item, idx) => {
      html += `<div class="eco-3d-match-card" data-id="${item.id}" data-side="right">${escapeHtml(withEmoji(item.text, idx))}</div>`;
    });
    html += '</div></div></div>';
    overlayInner.innerHTML = html;

    const wrap = overlayInner.querySelector('.eco-3d-matching-wrap');
    const svg = overlayInner.querySelector('.eco-3d-match-lines');
    const leftCards = overlayInner.querySelectorAll('.eco-3d-match-card[data-side="left"]');
    const rightCards = overlayInner.querySelectorAll('.eco-3d-match-card[data-side="right"]');

    function getCenter(el) {
      const r = el.getBoundingClientRect();
      const w = wrap.getBoundingClientRect();
      return { x: r.left - w.left + r.width / 2, y: r.top - w.top + r.height / 2 };
    }

    function updateDragLine(x, y) {
      if (!dragLine || !dragFromPos) return;
      dragLine.setAttribute('x2', x);
      dragLine.setAttribute('y2', y);
    }

    function endDrag(success, endEl) {
      if (!dragFromCard || !dragLine) return;
      const fromPos = dragFromPos;
      dragFromCard.classList.remove('eco-3d-drawing');
      if (success && endEl) {
        const toPos = getCenter(endEl);
        dragLine.classList.remove('eco-3d-line-drag');
        dragLine.classList.add('eco-3d-line-done');
        dragLine.setAttribute('x2', toPos.x);
        dragLine.setAttribute('y2', toPos.y);
        dragLine.setAttribute('marker-end', 'url(#eco-arrowhead-done)');
        dragFromCard.classList.add('eco-3d-matched');
        endEl.classList.add('eco-3d-matched');
        matchedCount++;
        if (matchedCount === pairs.length) {
          setTimeout(() => {
            overlayInner.innerHTML = '<div class="eco-3d-result"><h2>Perfect match!</h2><p>All pairs connected.</p></div>';
            recordGameComplete();
          }, 400);
        }
      } else {
        if (endEl) {
          endEl.classList.add('eco-3d-wrong');
          setTimeout(() => endEl.classList.remove('eco-3d-wrong'), 500);
        }
        dragLine.classList.add('eco-3d-line-wrong');
        setTimeout(() => dragLine && dragLine.remove(), 400);
      }
      dragLine = null;
      dragFromCard = null;
      dragFromPos = null;
    }

    function startDraw(card, clientX, clientY) {
      if (card.classList.contains('eco-3d-matched')) return;
      const pos = getCenter(card);
      const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
      line.setAttribute('x1', pos.x);
      line.setAttribute('y1', pos.y);
      line.setAttribute('x2', pos.x);
      line.setAttribute('y2', pos.y);
      line.setAttribute('stroke', 'var(--eco-primary, #4ecdc4)');
      line.setAttribute('stroke-width', '3');
      line.setAttribute('marker-end', 'url(#eco-arrowhead)');
      line.classList.add('eco-3d-line-drag');
      svg.appendChild(line);
      dragLine = line;
      dragFromCard = card;
      dragFromPos = pos;
      card.classList.add('eco-3d-drawing');
    }

    function moveDraw(clientX, clientY) {
      if (!dragLine || !wrap) return;
      const w = wrap.getBoundingClientRect();
      updateDragLine(clientX - w.left, clientY - w.top);
    }

    function upDraw(clientX, clientY) {
      if (!dragFromCard || !dragLine) return;
      const target = document.elementFromPoint(clientX, clientY);
      const rightCard = target ? target.closest('.eco-3d-match-card[data-side="right"]') : null;
      if (rightCard && !rightCard.classList.contains('eco-3d-matched')) {
        const leftId = dragFromCard.dataset.id;
        const rightId = rightCard.dataset.id;
        endDrag(leftId === rightId, rightCard);
      } else {
        dragLine.remove();
        dragFromCard.classList.remove('eco-3d-drawing');
        dragLine = null;
        dragFromCard = null;
        dragFromPos = null;
      }
    }

    leftCards.forEach((card) => {
      card.addEventListener('mousedown', (e) => {
        e.preventDefault();
        startDraw(card, e.clientX, e.clientY);
      });
      card.addEventListener('touchstart', (e) => {
        if (e.cancelable) e.preventDefault();
        startDraw(card, e.touches[0].clientX, e.touches[0].clientY);
      }, { passive: false });
    });

    overlayInner.addEventListener('mousemove', (e) => moveDraw(e.clientX, e.clientY));
    overlayInner.addEventListener('touchmove', (e) => {
      if (dragLine && e.cancelable) e.preventDefault();
      if (e.touches.length) moveDraw(e.touches[0].clientX, e.touches[0].clientY);
    }, { passive: false });
    overlayInner.addEventListener('mouseleave', () => {
      if (dragLine && dragFromCard) {
        dragLine.remove();
        dragFromCard.classList.remove('eco-3d-drawing');
        dragLine = null;
        dragFromCard = null;
        dragFromPos = null;
      }
    });
    overlayInner.addEventListener('mouseup', (e) => upDraw(e.clientX, e.clientY));
    overlayInner.addEventListener('touchend', (e) => {
      if (e.changedTouches.length) upDraw(e.changedTouches[0].clientX, e.changedTouches[0].clientY);
    });
  }

  // —— Legacy quiz (fallback) ——
  function runQuiz() {
    let qIndex = 0;
    const inner = overlayInner;
    function showQuestion() {
      if (qIndex >= questions.length) {
        recordGameComplete();
        inner.innerHTML = '<div class="eco-3d-result"><h2>Well done!</h2><p>Quiz complete.</p></div>';
        return;
      }
      const q = questions[qIndex];
      let opts = (q.options || []).map((opt) => `<button type="button" class="eco-3d-option" data-correct="${opt.is_correct ? '1' : '0'}">${escapeHtml(withEmoji(opt.text))}</button>`).join('');
      inner.innerHTML = `<div class="eco-3d-question-card"><p class="eco-3d-question-text">${escapeHtml(withEmoji(q.question_text))}</p><div class="eco-3d-options">${opts}</div></div>`;
      inner.querySelectorAll('.eco-3d-option').forEach((btn) => {
        btn.addEventListener('click', function () {
          const correct = this.dataset.correct === '1';
          inner.querySelectorAll('.eco-3d-option').forEach((b) => (b.style.pointerEvents = 'none'));
          this.classList.add(correct ? 'eco-3d-correct' : 'eco-3d-incorrect');
          qIndex++;
          setTimeout(showQuestion, 800);
        });
      });
    }
    showQuestion();
  }

  // —— Route to game type ——
  if (gameType === 'drag_drop' && categories.length > 0 && items.length > 0) {
    runDragDrop();
  } else if (gameType === 'matching' && pairs.length > 0) {
    runMatching();
  } else if (questions.length > 0) {
    runQuiz();
  } else {
    overlayInner.innerHTML = '<p class="eco-3d-no-questions">No game content. Try creating a new game.</p>';
  }
})();
