const ENDPOINT = "https://api.geheimesite.nl/currently-playing";
const FPS = 100;

let none = { playing: false };
let data = { playing: false };

let pulling = false;
let watcher;

const song = document.querySelector("#song");
const progress = document.querySelector("#progress");

async function pullSong() {
  pulling = true;

  const response = await fetch(ENDPOINT);

  if (response.status >= 400) data = none;
  else data = await response.json();

  if (watcher) window.clearInterval(watcher);
  if (data.playing) mountWatcher();

  renderSong();
  pulling = false;
}

function mountWatcher() {
  watcher = window.setInterval(() => {
    data.progress += FPS;
    renderSong();

    let finished = data.progress > data.duration;
    if (finished && !pulling) pullSong();
  }, FPS);
}

function renderSong() {
  if (data.playing) {
    if(data.track == data.album) {
      song.innerHTML = `
        <cite class="track">${data.track}</cite> 
        by <span class="artists">${data.artists.join(", ")}</span>
      `;
    } else {
      song.innerHTML = `
        <cite class="track">${data.track}</cite> 
        from <cite class="album">${data.album}</cite> 
        by <span class="artists">${data.artists.join(", ")}</span>
      `;
    }

    song.className = "playing";
    progress.innerText = `${fmtTime(data.progress)}/${fmtTime(data.duration)}`;
  } else {
    song.innerText = "Not playing anything right now.";
    song.className = "";
    progress.innerText = "";
  }
}

function fmtTime(ms) {
  const totalSeconds = Math.floor(ms / 1000);
  const minutes = Math.floor(totalSeconds / 60);
  const seconds = totalSeconds % 60;
  const formattedSeconds = seconds < 10 ? `0${seconds}` : seconds;
  return `${minutes}:${formattedSeconds}`;
}

// Kickstart the player.
pullSong();

// If something gets stuck this should correct it.
window.setInterval(() => {
  if (!pulling) pullSong();
}, 15000);
