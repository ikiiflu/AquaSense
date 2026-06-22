/**
 * AquaSense – script do frontend
 * Responsável por: relógio, atualização dos cards de métricas (/api/sensors),
 * dots de status da sidebar e recarga automática global da página.
 *
 * Geração de leituras:
 *   - Browser aberto: gerada e página recarregada a cada intervalo_atualizacao_seg
 *   - Browser fechado: cron (schedule:run) como fallback
 */

(function () {
  "use strict";

  var metaGet = function (name, fallback) {
    var el = document.querySelector('meta[name="' + name + '"]');
    return el ? el.content : fallback;
  };

  var CSRF_TOKEN          = metaGet("csrf-token", "");
  var REFRESH_INTERVAL_MS = parseInt(metaGet("refresh-interval", "60"), 10) * 1000;

  // ---- Relógio ----
  var clockEl = document.getElementById("statusbar-clock");
  function tickClock() {
    if (!clockEl) return;
    clockEl.textContent = new Date().toLocaleTimeString("pt-BR", {
      hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false
    });
  }

  // ---- Fetch sensores e atualiza cards ----
  var metricObs   = document.getElementById("metric-obstruction");
  var metricChuva = document.getElementById("metric-rainfall");
  var metricVazao = document.getElementById("metric-flow");

  function fetchMetrics() {
    fetch("/api/sensors")
      .then(function (res) { return res.json(); })
      .then(function (data) {
        var sensors = data.data || [];
        if (!sensors.length) return;

        var readings = sensors.map(function (s) { return s.reading; }).filter(Boolean);
        if (!readings.length) return;

        var avgObs  = readings.reduce(function (a, r) { return a + (r.obstrucao_pct   || 0); }, 0) / readings.length;
        var avgRain = readings.reduce(function (a, r) { return a + (r.precipitacao_mm  || 0); }, 0) / readings.length;
        var avgFlow = readings.reduce(function (a, r) { return a + (r.vazao_lps        || 0); }, 0) / readings.length;

        if (metricObs)   metricObs.innerHTML   = avgObs.toFixed(1)  + '<span style="font-size:1.4rem;opacity:0.7">%</span>';
        if (metricChuva) metricChuva.innerHTML = avgRain.toFixed(1) + '<span style="font-size:1.4rem;opacity:0.7"> mm</span>';
        if (metricVazao) metricVazao.innerHTML = avgFlow.toFixed(1) + '<span style="font-size:1.4rem;opacity:0.7"> L/s</span>';

        updateSidebarDots(sensors);
      })
      .catch(function (err) { console.warn("AquaSense: erro ao buscar métricas:", err); });
  }

  // ---- Atualiza dots de status da sidebar ----
  function updateSidebarDots(sensors) {
    var byId = {};
    sensors.forEach(function (s) { byId[s.id] = s.status; });
    document.querySelectorAll(".js-sensor-item").forEach(function (item) {
      var id  = item.dataset.sensorId;
      var dot = item.querySelector(".sensor-dot");
      if (dot && byId[id]) dot.className = "sensor-dot status-" + byId[id];
    });
  }

  // ---- Sidebar: seleção ----
  document.querySelectorAll(".js-sensor-item").forEach(function (item) {
    item.addEventListener("click", function () {
      document.querySelectorAll(".js-sensor-item").forEach(function (i) { i.classList.remove("is-selected"); });
      item.classList.add("is-selected");
    });
  });

  // ---- Sidebar mobile: hamburger toggle ----
  var sidebar  = document.querySelector(".app-sidebar");
  var overlay  = document.getElementById("sidebar-overlay");
  var menuBtn  = document.getElementById("mobile-menu-btn");
  var closeBtn = document.getElementById("sidebar-close-btn");

  function openSidebar() {
    sidebar.classList.add("is-open");
    overlay.classList.add("is-visible");
    document.body.style.overflow = "hidden";
  }

  function closeSidebar() {
    sidebar.classList.remove("is-open");
    overlay.classList.remove("is-visible");
    document.body.style.overflow = "";
  }

  if (menuBtn)  menuBtn.addEventListener("click", openSidebar);
  if (closeBtn) closeBtn.addEventListener("click", closeSidebar);
  if (overlay)  overlay.addEventListener("click", closeSidebar);

  document.querySelectorAll(".sidebar-nav a").forEach(function (link) {
    link.addEventListener("click", function () {
      if (window.innerWidth <= 960) closeSidebar();
    });
  });

  // ---- Recarga automática global ----
  var isMapPage          = window.location.pathname === "/map";
  var countdownEl        = document.getElementById("next-refresh-countdown");
  var mobileCountdownEl  = document.getElementById("mobile-refresh-countdown");
  var STORAGE_KEY        = "aquasense_next_refresh";

  if (!isMapPage && REFRESH_INTERVAL_MS >= 5000) {

    var now    = Date.now();
    var nextAt = parseInt(localStorage.getItem(STORAGE_KEY) || "0", 10);
    if (nextAt <= now) {
      nextAt = now + REFRESH_INTERVAL_MS;
      localStorage.setItem(STORAGE_KEY, nextAt);
    }

    function doRefresh() {
      localStorage.setItem(STORAGE_KEY, Date.now() + REFRESH_INTERVAL_MS);
      fetch("/api/leituras/gerar", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": CSRF_TOKEN, "Content-Type": "application/json" },
        body: JSON.stringify({ force: true })
      }).finally(function () { location.reload(); });
    }

    setInterval(function () {
      var remaining = Math.max(0, Math.round((nextAt - Date.now()) / 1000));
      if (countdownEl)       countdownEl.textContent       = remaining + "s";
      if (mobileCountdownEl) mobileCountdownEl.textContent = remaining + "s";
      if (Date.now() >= nextAt) {
        nextAt = Infinity;
        doRefresh();
      }
    }, 1000);

    var initialRemaining = Math.max(0, Math.round((nextAt - now) / 1000)) + "s";
    if (countdownEl)       countdownEl.textContent       = initialRemaining;
    if (mobileCountdownEl) mobileCountdownEl.textContent = initialRemaining;
  }

  // ---- Init ----
  tickClock();
  fetchMetrics();

  setInterval(tickClock,    1000);
  setInterval(fetchMetrics, 30000);

})();
