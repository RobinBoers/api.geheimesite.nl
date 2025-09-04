const ENDPOINT = "https://api.geheimesite.nl/listenbuds";

let none = { recent_listens: [] };

let stored = localStorage.getItem("listen-data");
let data = stored ? JSON.parse(stored) : none;

let pulling = false;
let watcher;

const container = document.querySelector("#listenbuds");

async function pullListens() {
  pulling = true;

  const response = await fetch(ENDPOINT);

  if (response.status >= 400) data = none;
  else data = await response.json();

  renderListens();
  saveListens();

  pulling = false;
}

function saveListens() {
  localStorage.setItem("listen-data", JSON.stringify(data));
}

function renderListens() {
  if (!data.recent_listens || data.recent_listens.length == 0) {
    container.innerHTML = '<div class="empty">No recent listens found.</div>';
    return;
  }

  container.innerHTML = `
    <div class="top-artists-section">
      <h2>Top Artists</h2>
      ${renderTopArtists()}
    </div>
    <div class="recents-section">
      <h2>Recent Listens</h2>
      ${renderRecentListens()}
    </div>
  `;
}


function renderTopArtists() {
  if (!data.top_artists || data.top_artists.length == 0) return '<div class="empty">No artist data available.</div>';

  return `
    <div class="artists-grid">
      ${data.top_artists.map((artist, index) => `
        <div class="artist-card">
          <div class="artist-rank">#${index + 1}</div>
          <img src="${artist.profile_picture}" alt="${artist.name}" class="artist-image" />
          <div class="artist-info">
            <div class="artist-name">${artist.name}</div>
            <div class="play-count">${artist.listen_count} plays</div>
          </div>
        </div>
      `).join('')}
    </div>
  `;
}

function renderRecentListens() {
  return data.recent_listens.map(listen => {
    const timeAgo = formatTimeAgo(listen.listened_ts);
    const artists = listen.artists.join(', ');
    
    return `
      <div class="listen-item">
        <div class="listen-main">
          <cite class="track">${listen.track}</cite>
          ${listen.track !== listen.album ? `from <cite class="album">${listen.album}</cite>` : ''}
          by <span class="artists">${artists}</span>
        </div>
        <div class="listen-meta">
          <span class="listen-time">${timeAgo}</span>
          ${listen.url ? `<a href="${listen.url}" class="spotify-link" target="_blank" rel="noopener">♫</a>` : ''}
        </div>
      </div>
    `;
  }).join('');
}

function formatTimeAgo(timestamp) {
  const now = Date.now() / 1000;
  const diff = now - timestamp;
  
  if (diff < 60) return 'just now';
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
  if (diff < 2592000) return `${Math.floor(diff / 86400)}d ago`;
  return new Date(timestamp * 1000).toLocaleDateString();
}

if (stored) renderListens();
pullListens();

// Pull again every 10 seconds.
window.setInterval(() => {
  if (!pulling) pullListens();
}, 10000);
