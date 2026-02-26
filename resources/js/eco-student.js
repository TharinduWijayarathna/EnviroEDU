/**
 * Student dashboard for small kids (grade 5 and below): topics, video, quizzes & games.
 * Data from window.ecoStudentData.topics (set by Blade).
 */

(function () {
  const data = window.ecoStudentData || { topics: [] };
  const topics = data.topics || [];
  const topicsListEl = document.getElementById('ecoTopicsList');
  const headerEl = document.getElementById('ecoGameHeader');
  const contentEl = document.getElementById('ecoGameContent');
  const gradeSelector = document.getElementById('ecoGradeSelector');
  const gradeForm = document.getElementById('ecoGradeForm');

  function toEmbedUrl(url) {
    if (!url) return null;
    try {
      const u = new URL(url);
      if (u.hostname.includes('youtube.com') && u.searchParams.get('v')) {
        return 'https://www.youtube.com/embed/' + u.searchParams.get('v');
      }
      if (u.hostname === 'youtu.be' && u.pathname.slice(1)) {
        return 'https://www.youtube.com/embed/' + u.pathname.slice(1);
      }
    } catch (_) {}
    return url;
  }

  function renderTopics() {
    if (!topicsListEl) return;
    if (topics.length === 0) {
      topicsListEl.innerHTML = '<p class="eco-kid-empty">No topics yet. Try another grade or come back later! 🌟</p>';
      return;
    }
    topicsListEl.innerHTML = topics
      .map(
        (topic, i) => `
      <div class="eco-topic-card" data-index="${i}">
        <div class="eco-topic-icon">📚</div>
        <div class="eco-topic-title">${escapeHtml(topic.title)}</div>
      </div>`
      )
      .join('');
    topicsListEl.querySelectorAll('.eco-topic-card').forEach((card) => {
      card.addEventListener('click', () => selectTopic(Number(card.dataset.index)));
    });
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function selectTopic(index) {
    const topic = topics[index];
    if (!topic || !headerEl || !contentEl) return;
    headerEl.textContent = topic.title;

    let html = '';
    if (topic.description) {
      html += '<p class="eco-kid-topic-desc">' + escapeHtml(topic.description) + '</p>';
    }
    if (topic.video_url) {
      const embedUrl = toEmbedUrl(topic.video_url);
      if (embedUrl && (embedUrl.includes('youtube.com/embed') || embedUrl.includes('youtu.be'))) {
        html += `
          <div class="eco-video-embed" style="width: 100%; max-width: 700px;">
            <iframe width="100%" height="100%" src="${escapeHtml(embedUrl)}" title="Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        `;
      } else {
        html += '<p style="margin-bottom: 1rem;"><a href="' + escapeHtml(topic.video_url) + '" target="_blank" rel="noopener" class="eco-btn">📺 Watch video</a></p>';
      }
    }
    const hasQuizzes = topic.quizzes && topic.quizzes.length > 0;
    const hasGames = topic.mini_games && topic.mini_games.length > 0;
    if (hasQuizzes || hasGames) {
      html += '<div class="eco-kid-actions"><h3 class="eco-kid-actions-title">Play a quiz or game 🎮</h3>';
      html += '<div class="eco-kid-action-buttons">';
      if (topic.quizzes) {
        topic.quizzes.forEach((q) => {
          html += '<a href="' + escapeHtml(q.play_url) + '" class="eco-btn eco-kid-action-btn">📝 ' + escapeHtml(q.title) + '</a>';
        });
      }
      if (topic.mini_games) {
        topic.mini_games.forEach((g) => {
          html += '<a href="' + escapeHtml(g.play_url) + '" class="eco-btn eco-kid-action-btn eco-kid-game-btn">🎮 ' + escapeHtml(g.title) + '</a>';
        });
      }
      html += '</div></div>';
    } else {
      html += '<p class="eco-kid-no-games">No games here yet. Try another topic! 🌟</p>';
    }
    contentEl.innerHTML = html;
  }

  function showWelcome() {
    if (!headerEl || !contentEl) return;
    headerEl.textContent = 'Choose something to do! 👇';
    contentEl.innerHTML = `
      <div class="eco-kid-placeholder">
        <div class="eco-kid-placeholder-visual" aria-hidden="true">
          <span class="eco-kid-placeholder-emoji">📚</span>
          <span class="eco-kid-placeholder-emoji">🎮</span>
          <span class="eco-kid-placeholder-emoji">🌟</span>
        </div>
        <p class="eco-kid-placeholder-text">Tap a topic on the left to learn, or pick a quiz or game to play!</p>
      </div>
    `;
  }

  if (gradeSelector && gradeForm) {
    gradeSelector.addEventListener('change', () => gradeForm.submit());
  }

  document.getElementById('ecoCloseBadgeBtn')?.addEventListener('click', () => {
    document.getElementById('ecoBadgeModal')?.classList.remove('show');
  });

  renderTopics();
  showWelcome();
})();
