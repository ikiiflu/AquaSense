/**
 * AquaSense — Dashboard Script
 * Responsável por: relógio, sparklines, métricas (/api/sensors),
 * atualização dos status dots da sidebar e geração automática de leituras.
 *
 * O mapa é inicializado diretamente no view map/operational_map.blade.php.
 */

(function () {
  "use strict";

  // Intervalo de leitura (segundos) lido do meta tag injetado pelo Blade
  var intervalMeta = document.querySelector('meta[name="reading-interval"]');
  var READING_INTERVAL_MS = intervalMeta ? (parseInt(intervalMeta.content, 10) * 1000) : 60000;

  var CSRF_TOKEN = (document.querySelector('meta[name="csrf-token"]') || {}).content || "";

  // ---- Clock ----
  var clockEl = document.getElementById("statusbar-clock");
  function tickClock() {
    if (!clockEl) return;
    clockEl.textContent = new Date().toLocaleTimeString("pt-BR", {
      hour: "2-digit", minute: "2-digit", second: "2-digit", hour12: false
    });
  }

  // ---- Sparklines (decorativo) ----
  function renderSparklines() {
    document.querySelectorAll(".metric-card-spark").forEach(function (el) {
      el.innerHTML = "";
      for (var i = 0; i < 24; i++) {
        var bar = document.createElement("div");
        bar.className = "metric-card-spark-bar";
        var h = 30 + Math.random() * 70;
        bar.style.height = h + "%";
        if (h > 75) bar.classList.add("is-high");
        el.appendChild(bar);
      }
    });
  }

  // ---- Geração automática de leituras ----
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
      .then(function (data) {
        if (data.inseridas > 0) {
          fetchMetrics();
        }
      })
      .catch(function (err) { console.warn("AquaSense: erro ao gerar leituras:", err); });
  }

  // ---- Fetch sensors e atualiza cards de métricas ----
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

        if (metricObs) {
          metricObs.innerHTML = Math.round(avgObs) + '%<span class="unit">obstrução</span>';
        }
        if (metricChuva) {
          metricChuva.innerHTML = avgRain.toFixed(1) + '<span class="unit">mm</span>';
        }
        if (metricVazao) {
          metricVazao.innerHTML = Math.round(avgFlow) + '<span class="unit">L/s</span>';
        }

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
      if (dot && byId[id]) {
        dot.className = "sensor-dot status-" + byId[id];
      }
    });
  }

  // ---- Sidebar: click para marcar selecionado ----
  document.querySelectorAll(".js-sensor-item").forEach(function (item) {
    item.addEventListener("click", function () {
      document.querySelectorAll(".js-sensor-item").forEach(function (i) {
        i.classList.remove("is-selected");
      });
      item.classList.add("is-selected");
    });
  });

  // ---- Init ----
  renderSparklines();
  tickClock();
  fetchMetrics();
  autoGerar();

  setInterval(tickClock, 1000);
  setInterval(fetchMetrics, 30000);
  setInterval(autoGerar, READING_INTERVAL_MS);

})();
