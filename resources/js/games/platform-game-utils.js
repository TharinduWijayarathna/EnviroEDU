/**
 * Shared helpers for platform games: record completion and show win overlay.
 */
export function recordComplete(slug, progressUrl, csrfToken, details = {}) {
  if (!progressUrl || !csrfToken) return;
  fetch(progressUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
    body: JSON.stringify({ platform_game_slug: slug, completed: true, details }),
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.new_badges && data.new_badges.length > 0 && window.ecoShowBadgeModal) {
        data.new_badges.forEach((b) => window.ecoShowBadgeModal(b));
      }
    })
    .catch(() => {});
}

export function showWinUI(emoji = '🌟', title = 'Great Job!', message = "You've completed this lesson!") {
  if (document.getElementById('eco-platform-game-win')) return;
  const backUrl = window.location.pathname.includes('/test') ? '/test' : '/dashboard/student/games';
  const overlay = document.createElement('div');
  overlay.id = 'eco-platform-game-win';
  overlay.style.cssText =
    'position:fixed;inset:0;background:rgba(0,0,0,0.65);display:flex;align-items:center;justify-content:center;z-index:99999;backdrop-filter:blur(6px);';
  overlay.innerHTML = `
    <div class="eco-platform-game-win-card" style="background:linear-gradient(180deg,#fffef5 0%,#fffde7 100%);padding:2.5rem;border-radius:28px;text-align:center;max-width:420px;border:5px solid #ffc107;box-shadow:0 24px 64px rgba(0,0,0,0.35);">
      <div style="font-size:4.5rem;margin-bottom:0.5rem;">${emoji}</div>
      <h2 style="font-size:1.85rem;color:#2e7d32;margin:0 0 0.75rem;font-family:'Bubblegum Sans',cursive;">${title}</h2>
      <p style="font-size:1.15rem;color:#555;margin:0;line-height:1.6;">${message}</p>
      <a href="${backUrl}" style="display:inline-block;margin-top:1.5rem;padding:14px 32px;background:#4caf50;color:#fff!important;border-radius:50px;text-decoration:none;font-weight:700;font-size:1.1rem;box-shadow:0 4px 14px rgba(76,175,80,0.5);transition:transform 0.2s;">Back to Games</a>
    </div>
  `;
  overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.remove(); });
  document.body.appendChild(overlay);
}
