<?php

/**
 * Template Name: Media
 */

get_header();
?>
<main class="main-content">

  <div class="container">

    <section class="section-radio">

      <div class="player-controls">
        <button id="playBtn" class="play-btn">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M8 5v14l11-7z" />
          </svg>
        </button>
        <button id="pauseBtn" class="pause-btn" style="display:none;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <rect x="6" y="4" width="4" height="16" />
            <rect x="14" y="4" width="4" height="16" />
          </svg>
        </button>
      </div>

      <div class="volume-control">
        <input type="range" id="volumeSlider" min="0" max="1" step="0.1" value="0.5">
      </div>

      <audio id="radioPlayer" preload="none" crossorigin="anonymous">
        <source src="https://studio19.radiolize.com/radio/8120/radio.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>

      <div class="equalizer-container">
        <canvas id="equalizer" width="500" height="50"></canvas>
      </div>

    </section>

  </div>

  <section class="section-youtube">

    <div class="container">

      <h2>HÖR BERLIN</h2>

      <div id="youtube-playlist-1" class="video-grid"></div>n

      <script>
        const API_KEY = "<?php echo esc_js(YOUTUBE_API_KEY); ?>";
        const PLAYLIST_ID = "PLue4XlmLp3HJtxsWGTt3FoJXCUTfjwUJB";
        const container = document.getElementById('youtube-playlist-1');

        fetch(`https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=20&playlistId=${PLAYLIST_ID}&key=${API_KEY}`)
          .then(res => res.json())
          .then(data => {
            data.items.forEach(item => {
              const videoId = item.snippet.resourceId.videoId;
              const title = item.snippet.title;
              const thumbnail = item.snippet.thumbnails.medium.url;

              container.innerHTML += `
            <div class="video-item">
              <a href="https://www.youtube.com/watch?v=${videoId}" target="_blank">
                <img src="${thumbnail}" alt="${title}" style="width:100%;max-width:400px;border-radius:8px;">
              </a>
              <p>${title}</p>
            </div>
          `;
            });
          });
      </script>
    </div>

  </section>

  <section class="section-soundcloud">
    <div class="container">
      <h2>Radio MI</h2>

      <iframe
        width="100%"
        height="500"
        scrolling="yes"
        frameborder="no"
        allow="autoplay"
        src="https://w.soundcloud.com/player/?url=https://soundcloud.com/radio-mi&color=%23333333&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=true&show_teaser=true">
      </iframe>

    </div>
  </section>

</main>

<script>
  let audioContext;
  let analyser;
  let dataArray;
  let animationId;

  const audio = document.getElementById('radioPlayer');
  const playBtn = document.getElementById('playBtn');
  const pauseBtn = document.getElementById('pauseBtn');
  const volumeSlider = document.getElementById('volumeSlider');
  const canvas = document.getElementById('equalizer');
  const ctx = canvas.getContext('2d');

  // Defer canvas sizing until layout is ready
  function resizeCanvas() {
    const dpr = window.devicePixelRatio || 1;
    const displayWidth = canvas.offsetWidth;
    const displayHeight = canvas.offsetHeight;

    if (displayWidth === 0 || displayHeight === 0) return;

    canvas.width = displayWidth * dpr;
    canvas.height = displayHeight * dpr;
    ctx.scale(dpr, dpr);
    canvas.style.width = displayWidth + 'px';
    canvas.style.height = displayHeight + 'px';
  }

  // Run after layout, not immediately
  window.addEventListener('load', resizeCanvas);
  window.addEventListener('resize', () => {
    ctx.setTransform(1, 0, 0, 1, 0, 0); // reset transform before rescaling
    resizeCanvas();
  });

  function initAudioContext() {
    if (!audioContext) {
      audioContext = new (window.AudioContext || window.webkitAudioContext)();
      analyser = audioContext.createAnalyser();
      const source = audioContext.createMediaElementSource(audio);

      source.connect(analyser);
      analyser.connect(audioContext.destination);

      analyser.fftSize = 1024;
      const bufferLength = analyser.frequencyBinCount;
      dataArray = new Uint8Array(bufferLength);
    }
  }

  function drawEqualizer() {
    if (!analyser) return;

    analyser.getByteFrequencyData(dataArray);

    const dpr = window.devicePixelRatio || 1;
    const canvasWidth = canvas.width / dpr;
    const canvasHeight = canvas.height / dpr;

    ctx.fillStyle = 'black';
    ctx.fillRect(0, 0, canvasWidth, canvasHeight);

    const barCount = Math.min(dataArray.length, 100);
    const barWidth = Math.floor(canvasWidth / barCount) - 1;
    let x = 0;

    for (let i = 0; i < barCount; i++) {
      const barHeight = Math.floor((dataArray[i] / 255) * canvasHeight);
      ctx.fillStyle = 'white';
      ctx.fillRect(Math.floor(x), canvasHeight - barHeight, barWidth, barHeight);
      x += barWidth + 1;
    }

    animationId = requestAnimationFrame(drawEqualizer);
  }

  playBtn.addEventListener('click', async () => {
    try {
      // iOS requires AudioContext to be created/resumed inside a user gesture
      initAudioContext();
      if (audioContext.state === 'suspended') {
        await audioContext.resume();
      }

      // iOS Safari sometimes needs a fresh load of the stream
      if (audio.readyState === 0) {
        audio.load();
      }

      await audio.play();

      // Re-check canvas size in case it was zero at load time
      resizeCanvas();

      drawEqualizer();
      playBtn.style.display = 'none';
      pauseBtn.style.display = 'inline-block';
    } catch (error) {
      console.error('Playback error:', error);
    }
  });

  pauseBtn.addEventListener('click', () => {
    audio.pause();
    cancelAnimationFrame(animationId);
    pauseBtn.style.display = 'none';
    playBtn.style.display = 'inline-block';
  });

  volumeSlider.addEventListener('input', (e) => {
    audio.volume = e.target.value;
  });

  audio.addEventListener('ended', () => {
    cancelAnimationFrame(animationId);
    pauseBtn.style.display = 'none';
    playBtn.style.display = 'inline-block';
  });
</script>

<?php get_footer(); ?>