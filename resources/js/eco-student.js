/**
 * Student dashboard: dynamic topics with optional video lesson and quizzes/games.
 * Data comes from window.ecoStudentData.topics (set by Blade).
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
      topicsListEl.innerHTML = '<p style="font-size: 0.9rem; color: #666;">No topics yet. Check back later or try another grade!</p>';
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
      html += '<p style="text-align: center; color: #555; margin-bottom: 1rem;">' + escapeHtml(topic.description) + '</p>';
    }
    if (topic.video_url) {
      const embedUrl = toEmbedUrl(topic.video_url);
      if (embedUrl && (embedUrl.includes('youtube.com/embed') || embedUrl.includes('youtu.be'))) {
        html += `
          <div class="eco-video-embed" style="width: 100%; max-width: 700px;">
            <iframe width="100%" height="100%" src="${escapeHtml(embedUrl)}" title="Video lesson" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        `;
      } else {
        html += '<p style="margin-bottom: 1rem;"><a href="' + escapeHtml(topic.video_url) + '" target="_blank" rel="noopener" class="eco-btn">📺 Watch Video Lesson</a></p>';
      }
    }
    const hasQuizzes = topic.quizzes && topic.quizzes.length > 0;
    const hasGames = topic.mini_games && topic.mini_games.length > 0;
    if (hasQuizzes || hasGames) {
      html += '<div style="text-align: center; width: 100%; max-width: 600px;"><h3 style="font-family: \'Bubblegum Sans\', cursive; margin-bottom: 1rem;">Quizzes &amp; Games</h3>';
      html += '<div style="display: flex; flex-direction: column; gap: 0.75rem; align-items: center;">';
      if (topic.quizzes) {
        topic.quizzes.forEach((q) => {
          html += '<a href="' + escapeHtml(q.play_url) + '" target="_blank" class="eco-btn" style="width: 100%; max-width: 320px; text-align: center;">📝 ' + escapeHtml(q.title) + '</a>';
        });
      }
      if (topic.mini_games) {
        topic.mini_games.forEach((g) => {
          html += '<a href="' + escapeHtml(g.play_url) + '" target="_blank" class="eco-btn" style="width: 100%; max-width: 320px; text-align: center; background: #2C3E50;">🎮 ' + escapeHtml(g.title) + '</a>';
        });
      }
      html += '</div></div>';
    } else {
      html += '<p style="color: #666;">No quizzes or games in this topic yet.</p>';
    }
    contentEl.innerHTML = html;
  }

  function showWelcome() {
    if (!headerEl || !contentEl) return;
    headerEl.textContent = 'Select a topic to start!';
    contentEl.innerHTML = `
      <div style="text-align: center; color: #555;">
        <div class="eco-books-stack">
          <div class="eco-book green"></div>
          <div class="eco-book blue"></div>
          <div class="eco-book orange"></div>
        </div>
        <p style="font-size: 1.15rem;">Pick a topic from the left, watch the video lesson (if any), then play the quiz or game!</p>
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
