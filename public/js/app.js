/**
 * AquaSense - Dashboard Script
 * Responsavel por: relogio, metricas (/api/sensors), dots da sidebar,
 * geracao automatica de leituras e recarga automatica da pagina.
 */

(function () {
  "use strict";

  var metaGet = function (name, fallback) {
    var el = document.querySelector('meta[name="' + name + '"]');
    return el ? el.content : fallback;
  };

  var CSRF_TOKEN          = metaGet("csrf-token", "");
  var READING_INTERVAL_MS = parseInt(metaGet("reading-interval", "60"), 10) * 1000;
  var REFRESH_MODE        = metaGet("refresh-mode", "manual");
  var REFRESH_INTERVAL_MS = parseInt(metaGet("refresh-interval", "60"), 10) * 1000;

  // ---- Relogio ----
  var clockEl = document.getElementById("statusbar-clock");
  function tickClock() {
    if (!clockEl) return;
    clockEl.textContent = new Date().toLocaleTimeString("pt-BR", {
      hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false
    });
  }

  // ---- Geracao automatica de leituras ----
  function autoGerar() {
    fetch("/api/leituras/gerar", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": CSRF_TOKEN,
        "Accept": "application/json"
      }
    })
      .then(function (res) { return res.json(); })
      .then(function (data) { if (data.inseridas > 0) fetchMetrics(); })
      .catch(function (err) { console.warn("AquaSense: erro ao gerar leituras:", err); });
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

        if (metricObs)   metricObs.textContent   = Math.round(avgObs * 10) / 10;
        if (metricChuva) metricChuva.textContent = avgRain.toFixed(1);
        if (metricVazao) metricVazao.textContent = Math.round(avgFlow);

        updateSidebarDots(sensors);
      })
      .catch(function (err) { console.warn("AquaSense: erro ao buscar metricas:", err); });
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

  // ---- Sidebar: selecao ----
  document.querySelectorAll(".js-sensor-item").forEach(function (item) {
    item.addEventListener("click", function () {
      document.querySelectorAll(".js-sensor-item").forEach(function (i) { i.classList.remove("is-selected"); });
      item.classList.add("is-selected");
    });
  });

  // ---- Recarga automática global (timer persiste entre navegações via localStorage) ----
  var isMapPage = window.location.pathname === "/map";

  if (!isMapPage && REFRESH_MODE === "automatico" && REFRESH_INTERVAL_MS >= 5000) {

    var countdownEl = document.getElementById("next-refresh-countdown");
    var STORAGE_KEY = "aquasense_next_refresh";

    // Lê quando a próxima atualização está agendada; se não existir ou já passou,
    // agenda a partir de agora (primeiro ciclo após configurar a função).
    var now       = Date.now();
    var nextAt    = parseInt(localStorage.getItem(STORAGE_KEY) || "0", 10);
    if (nextAt <= now) {
      nextAt = now + REFRESH_INTERVAL_MS;
      localStorage.setItem(STORAGE_KEY, nextAt);
    }

    function doRefresh() {
      // Agenda já o próximo ciclo antes de recarregar para que a próxima página
      // leia o valor correto no localStorage.
      localStorage.setItem(STORAGE_KEY, Date.now() + REFRESH_INTERVAL_MS);

      var controller = new AbortController();
      var fallback   = setTimeout(function () {
        controller.abort();
        location.reload();
      }, 8000);

      fetch("/api/leituras/gerar?force=1", {
        method:  "POST",
        headers: { "X-CSRF-TOKEN": CSRF_TOKEN, "Accept": "application/json" },
        signal:  controller.signal
      }).finally(function () {
        clearTimeout(fallback);
        location.reload();
      });
    }

    // Tick a cada segundo: atualiza contador e dispara quando chegar a hora
    setInterval(function () {
      var remaining = Math.max(0, Math.round((nextAt - Date.now()) / 1000));
      if (countdownEl) countdownEl.textContent = remaining + "s";
      if (Date.now() >= nextAt) {
        nextAt = Infinity; // evita disparos duplos enquanto o fetch corre
        doRefresh();
      }
    }, 1000);

    // Exibe valor inicial imediatamente
    if (countdownEl) {
      countdownEl.textContent = Math.max(0, Math.round((nextAt - now) / 1000)) + "s";
    }
  }

  // ---- Init ----
  tickClock();
  fetchMetrics();
  autoGerar();

  setInterval(tickClock, 1000);
  setInterval(fetchMetrics, 30000);
  setInterval(autoGerar, READING_INTERVAL_MS);

})();
