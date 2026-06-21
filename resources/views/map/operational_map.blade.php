@extends('layout.body')

@section('title', 'AquaSense — Mapa Operacional')

@section('content')
    <div class="map-section" style="height: calc(100vh - 120px); display: flex; flex-direction: column; position: relative;">
        <div class="map-overlay-top">
            <div class="map-pill" id="map-sensor-count" aria-label="Sensores no mapa">{{ $sensors->count() }} sensores</div>
            <div class="map-pill" id="map-time" aria-live="off">--:--</div>
            <div class="map-pill" style="color:var(--flow);font-size:0.72rem">
                Clique no mapa para adicionar um sensor
            </div>
            <button onclick="openBairrosModal()" class="map-pill" style="cursor:pointer;border:1px solid var(--line);background:var(--panel)">
                Gerenciar Bairros
            </button>
        </div>
        <div id="city-map" class="map-container" style="flex: 1; min-height: 100%;"></div>
    </div>

    {{-- ══ Modal Sensor (add / edit) ══ --}}
    <div id="sensor-modal" class="smodal-overlay">
        <div class="smodal-box">
            <div class="smodal-header">
                <h2 id="modal-title" class="smodal-title">Adicionar sensor</h2>
                <button onclick="closeSensorModal()" class="smodal-close">&times;</button>
            </div>

            <form id="sensor-form" onsubmit="submitSensorForm(event)">
                <input type="hidden" id="sensor-id" value="">

                <div class="smodal-field">
                    <label class="smodal-label">Código <span class="smodal-required">*</span></label>
                    <input type="text" id="field-code" class="smodal-input" placeholder="Ex.: AQS-011" maxlength="20" required>
                    <div id="err-codigo" class="smodal-error"></div>
                </div>

                <div class="smodal-field">
                    <label class="smodal-label">Nome <span class="smodal-required">*</span></label>
                    <input type="text" id="field-name" class="smodal-input" placeholder="Ex.: Bueiro Central" maxlength="100" required>
                    <div id="err-nome" class="smodal-error"></div>
                </div>

                <div class="smodal-field">
                    <label class="smodal-label">Endereço <span class="smodal-required">*</span></label>
                    <input type="text" id="field-address" class="smodal-input" placeholder="Ex.: Av. Brasil, 100" maxlength="200" required>
                    <div id="err-endereco" class="smodal-error"></div>
                </div>

                <div class="smodal-field">
                    <label class="smodal-label">Bairro <span class="smodal-required">*</span></label>
                    <div style="display:flex;gap:0.5rem;align-items:center">
                        <select id="field-bairro-id" class="smodal-input" required style="flex:1">
                            <option value="">Carregando…</option>
                        </select>
                        <button type="button" onclick="openBairrosModal()"
                                style="flex-shrink:0;padding:0.5rem 0.7rem;background:var(--panel);border:1px solid var(--line);
                                       border-radius:var(--radius-md);cursor:pointer;color:var(--ink-dim);font-size:0.82rem;
                                       font-family:var(--font-body);white-space:nowrap">
                            + Novo
                        </button>
                    </div>
                    <div id="err-bairro_id" class="smodal-error"></div>
                </div>

                <div class="smodal-grid-2">
                    <div class="smodal-field">
                        <label class="smodal-label">Latitude <span class="smodal-required">*</span></label>
                        <input type="number" id="field-lat" class="smodal-input" step="0.0000001" placeholder="-19.7880000" required>
                        <div id="err-latitude" class="smodal-error"></div>
                    </div>
                    <div class="smodal-field">
                        <label class="smodal-label">Longitude <span class="smodal-required">*</span></label>
                        <input type="number" id="field-lng" class="smodal-input" step="0.0000001" placeholder="-42.1400000" required>
                        <div id="err-longitude" class="smodal-error"></div>
                    </div>
                </div>

                <div id="modal-error" class="smodal-error smodal-error--global"></div>

                <div class="smodal-actions">
                    <button type="submit" class="smodal-btn-primary" id="modal-submit-btn">Salvar sensor</button>
                    <button type="button" onclick="closeSensorModal()" class="smodal-btn-secondary">Cancelar</button>
                    <button type="button" id="modal-delete-btn" onclick="deleteSensor()" class="smodal-btn-danger smodal-btn-danger--end" style="display:none">
                        Excluir
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══ Modal Bairros (CRUD) ══ --}}
    <div id="bairros-modal" class="smodal-overlay">
        <div class="smodal-box" style="width:440px">
            <div class="smodal-header">
                <h2 class="smodal-title">Gerenciar Bairros</h2>
                <button onclick="closeBairrosModal()" class="smodal-close">&times;</button>
            </div>

            {{-- Add / edit form --}}
            <div style="margin-bottom:1rem">
                <label class="smodal-label" id="bairro-form-label">Novo bairro</label>
                <div style="display:flex;gap:0.5rem;align-items:flex-start">
                    <div style="flex:1">
                        <input type="text" id="field-bairro-nome" class="smodal-input" placeholder="Nome do bairro" maxlength="100">
                        <div id="err-bairro-nome" class="smodal-error"></div>
                    </div>
                    <div style="display:flex;gap:0.4rem;flex-shrink:0">
                        <button type="button" onclick="saveBairro()" class="smodal-btn-primary" style="padding:0.5rem 1rem">
                            Salvar
                        </button>
                        <button type="button" id="bairro-cancel-edit" onclick="cancelBairroEdit()"
                                class="smodal-btn-secondary" style="display:none;padding:0.5rem 0.75rem">
                            ✕
                        </button>
                    </div>
                </div>
                <input type="hidden" id="bairro-edit-id" value="">
            </div>

            {{-- List --}}
            <div style="border-top:1px solid var(--line);padding-top:0.75rem">
                <div id="bairros-list" style="display:flex;flex-direction:column;gap:0.35rem;max-height:280px;overflow-y:auto">
                    <div style="color:var(--ink-dim);font-size:0.8rem;text-align:center;padding:1rem">Carregando…</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
.smodal-overlay          { display:none;position:fixed;inset:0;z-index:9000;align-items:center;justify-content:center;background:rgba(0,0,0,0.6); }
.smodal-box              { background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:1.75rem;width:460px;max-width:95vw;max-height:90vh;overflow-y:auto; }
.smodal-header           { display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem; }
.smodal-title            { font-size:1rem;font-weight:700;color:var(--ink); }
.smodal-close            { background:none;border:none;cursor:pointer;color:var(--ink-dim);font-size:1.25rem;line-height:1; }
.smodal-field            { margin-bottom:1rem; }
.smodal-grid-2           { display:grid;grid-template-columns:1fr 1fr;gap:0.75rem; }
.smodal-label            { display:block;font-size:0.78rem;font-weight:600;color:var(--ink-dim);margin-bottom:0.35rem;text-transform:uppercase;letter-spacing:0.05em; }
.smodal-required         { color:var(--status-critico); }
.smodal-input            { width:100%;background:var(--void);border:1px solid var(--line);color:var(--ink);padding:0.52rem 0.75rem;border-radius:var(--radius-md);font-size:0.9rem;font-family:var(--font-body);transition:border-color var(--transition-fast);box-sizing:border-box; }
.smodal-input:focus      { outline:none;border-color:var(--flow); }
.smodal-error            { font-size:0.75rem;color:var(--status-critico);margin-top:0.3rem;min-height:1rem; }
.smodal-error--global    { margin-bottom:0.75rem; }
.smodal-actions          { display:flex;align-items:center;gap:0.75rem;margin-top:1.25rem; }
.smodal-btn-primary      { padding:0.55rem 1.25rem;background:var(--flow);color:var(--void);font-weight:700;font-size:0.85rem;border:none;border-radius:var(--radius-md);cursor:pointer;font-family:var(--font-body); }
.smodal-btn-primary:hover      { opacity:0.85; }
.smodal-btn-secondary    { padding:0.55rem 1.25rem;background:transparent;color:var(--ink-dim);font-size:0.85rem;border:1px solid var(--line);border-radius:var(--radius-md);cursor:pointer;font-family:var(--font-body); }
.smodal-btn-secondary:hover    { border-color:var(--ink-dim); }
.smodal-btn-danger       { padding:0.55rem 1.25rem;background:transparent;color:var(--status-critico);font-size:0.85rem;border:1px solid var(--status-critico);border-radius:var(--radius-md);cursor:pointer;font-family:var(--font-body); }
.smodal-btn-danger:hover       { background:var(--status-critico-dim); }
.smodal-btn-danger--end  { margin-left:auto; }

.map-dot { width:16px;height:16px;border-radius:50%;border:2.5px solid rgba(255,255,255,0.5);box-shadow:0 0 6px rgba(0,0,0,0.5);cursor:pointer;transition:transform 0.15s; }
.map-dot:hover { transform:scale(1.4); }

.bairro-item { display:flex;align-items:center;gap:0.5rem;padding:0.4rem 0.6rem;border-radius:6px;background:var(--void);border:1px solid var(--line); }
.bairro-item-nome { flex:1;font-size:0.88rem;color:var(--ink); }
.bairro-btn { padding:0.2rem 0.55rem;font-size:0.72rem;border-radius:4px;border:1px solid var(--line);cursor:pointer;background:transparent;color:var(--ink-dim);font-family:var(--font-body); }
.bairro-btn:hover { border-color:var(--flow);color:var(--flow); }
.bairro-btn-del:hover { border-color:var(--status-critico)!important;color:var(--status-critico)!important; }
</style>
@endpush

@push('scripts')
<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">
<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>
<script>window.AQUASENSE_SENSORS = {!! $sensorsJson !!};</script>
<script>
(function () {
    'use strict';

    var CSRF_TOKEN    = '{{ csrf_token() }}';
    var STATUS_COLORS = { ok: '#00D4AA', atencao: '#F59E0B', risco: '#F97316', critico: '#EF4444' };

    var map;
    var markersMap  = {};
    var sensors     = window.AQUASENSE_SENSORS || [];
    var bairrosList = [];

    // ── Utilitário ───────────────────────────────────────────────
    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ── Map time pill ─────────────────────────────────────────────
    function tickMapTime() {
        var el = document.getElementById('map-time');
        if (el) el.textContent = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', hour12: false });
        setTimeout(tickMapTime, 60000);
    }
    tickMapTime();

    // ── Marcadores ───────────────────────────────────────────────
    function buildDot(status) {
        var el = document.createElement('div');
        el.className = 'map-dot';
        el.style.backgroundColor = STATUS_COLORS[status] || STATUS_COLORS.ok;
        return el;
    }

    function addMarker(s) {
        if (markersMap[s.id]) markersMap[s.id].marker.remove();
        var dot = buildDot(s.status);
        dot.title = s.nome + (s.bairro ? ' — ' + s.bairro : '');
        dot.addEventListener('click', function (e) { e.stopPropagation(); openEditModal(s); });
        var marker = new maplibregl.Marker({ element: dot, anchor: 'center' })
            .setLngLat([s.lng || s.longitude, s.lat || s.latitude])
            .addTo(map);
        markersMap[s.id] = { marker: marker, data: s };
    }

    function removeMarker(id) {
        if (markersMap[id]) { markersMap[id].marker.remove(); delete markersMap[id]; }
    }

    function updateSensorCount() {
        var el = document.getElementById('map-sensor-count');
        if (el) el.textContent = Object.keys(markersMap).length + ' sensores';
    }

    // ── Mapa ──────────────────────────────────────────────────────
    map = new maplibregl.Map({
        container: 'city-map',
        style: 'https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json',
        center: [-42.140, -19.790],
        zoom: 14,
        attributionControl: false
    });
    map.addControl(new maplibregl.NavigationControl(), 'top-right');
    map.on('load', function () { sensors.forEach(addMarker); updateSensorCount(); });
    map.on('click', function (e) {
        if (e.originalEvent.target.closest('.maplibregl-marker')) return;
        openAddModal(e.lngLat.lat, e.lngLat.lng);
    });

    document.querySelectorAll('.js-sensor-item').forEach(function (item) {
        item.addEventListener('click', function () {
            var lat = parseFloat(item.dataset.lat);
            var lng = parseFloat(item.dataset.lng);
            if (map) map.flyTo({ center: [lng, lat], zoom: 16, duration: 1200 });
            document.querySelectorAll('.js-sensor-item').forEach(function (i) { i.classList.remove('is-selected'); });
            item.classList.add('is-selected');
        });
    });

    // ── Bairros: busca sempre fresca da API ───────────────────────
    function loadBairros(callback) {
        fetch('/api/bairros', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            bairrosList = data.data || [];
            if (callback) callback();
        })
        .catch(function () { if (callback) callback(); });
    }

    function populateBairroSelect(selectedId) {
        var sel = document.getElementById('field-bairro-id');
        sel.innerHTML = '<option value="">Selecione o bairro…</option>';
        bairrosList.forEach(function (b) {
            var opt = document.createElement('option');
            opt.value = b.id;
            opt.textContent = b.nome;
            if (selectedId && Number(b.id) === Number(selectedId)) opt.selected = true;
            sel.appendChild(opt);
        });
    }

    // ── Modal de sensor ───────────────────────────────────────────
    var sensorModal = document.getElementById('sensor-modal');

    function clearSensorErrors() {
        ['codigo','nome','endereco','bairro_id','latitude','longitude'].forEach(function (f) {
            var el = document.getElementById('err-' + f);
            if (el) el.textContent = '';
        });
        document.getElementById('modal-error').textContent = '';
    }

    window.openAddModal = function (lat, lng) {
        clearSensorErrors();
        document.getElementById('modal-title').textContent = 'Adicionar sensor';
        document.getElementById('sensor-id').value = '';
        document.getElementById('field-code').value = '';
        document.getElementById('field-name').value = '';
        document.getElementById('field-address').value = '';
        document.getElementById('field-lat').value = lat ? lat.toFixed(7) : '';
        document.getElementById('field-lng').value = lng ? lng.toFixed(7) : '';
        document.getElementById('modal-delete-btn').style.display = 'none';
        document.getElementById('modal-submit-btn').textContent = 'Adicionar sensor';
        loadBairros(function () {
            populateBairroSelect(null);
            sensorModal.style.display = 'flex';
        });
    };

    window.openEditModal = function (s) {
        clearSensorErrors();
        document.getElementById('modal-title').textContent = 'Editar sensor';
        document.getElementById('sensor-id').value = s.id;
        document.getElementById('field-code').value = s.codigo || '';
        document.getElementById('field-name').value = s.nome   || '';
        document.getElementById('field-address').value = s.endereco || '';
        document.getElementById('field-lat').value = (+(s.lat || s.latitude  || 0)).toFixed(7);
        document.getElementById('field-lng').value = (+(s.lng || s.longitude || 0)).toFixed(7);
        document.getElementById('modal-delete-btn').style.display = 'inline-block';
        document.getElementById('modal-submit-btn').textContent = 'Salvar alterações';
        loadBairros(function () {
            populateBairroSelect(s.bairro_id);
            sensorModal.style.display = 'flex';
        });
    };

    window.closeSensorModal = function () { sensorModal.style.display = 'none'; };
    sensorModal.addEventListener('click', function (e) { if (e.target === sensorModal) closeSensorModal(); });

    // ── CRUD sensor ───────────────────────────────────────────────
    window.submitSensorForm = function (e) {
        e.preventDefault();
        clearSensorErrors();
        var id      = document.getElementById('sensor-id').value;
        var payload = {
            codigo:    document.getElementById('field-code').value.trim(),
            nome:      document.getElementById('field-name').value.trim(),
            endereco:  document.getElementById('field-address').value.trim(),
            bairro_id: parseInt(document.getElementById('field-bairro-id').value) || null,
            latitude:  parseFloat(document.getElementById('field-lat').value),
            longitude: parseFloat(document.getElementById('field-lng').value),
        };
        fetch(id ? '/api/sensors/' + id : '/api/sensors', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json().then(function (b) { return { status: r.status, body: b }; }); })
        .then(function (r) {
            if (r.status === 422) {
                var errs = r.body.errors || {};
                Object.keys(errs).forEach(function (f) {
                    var el = document.getElementById('err-' + f);
                    if (el) el.textContent = errs[f][0];
                });
                return;
            }
            if (r.status >= 400) { document.getElementById('modal-error').textContent = r.body.message || 'Erro ao salvar.'; return; }
            addMarker(r.body.data);
            updateSensorCount();
            closeSensorModal();
        })
        .catch(function () { document.getElementById('modal-error').textContent = 'Erro de comunicação.'; });
    };

    window.deleteSensor = function () {
        var id = document.getElementById('sensor-id').value;
        if (!id || !confirm('Excluir este sensor? Esta ação não pode ser desfeita.')) return;
        fetch('/api/sensors/' + id, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        })
        .then(function (r) {
            if (r.status === 204) { removeMarker(parseInt(id)); updateSensorCount(); closeSensorModal(); }
            else document.getElementById('modal-error').textContent = 'Erro ao excluir sensor.';
        });
    };

    // ── Modal de bairros ─────────────────────────────────────────
    var bairrosModal = document.getElementById('bairros-modal');

    function renderBairrosList() {
        var container = document.getElementById('bairros-list');
        if (!bairrosList.length) {
            container.innerHTML = '<div style="color:var(--ink-dim);font-size:0.8rem;text-align:center;padding:1rem">Nenhum bairro cadastrado</div>';
            return;
        }
        container.innerHTML = '';
        bairrosList.forEach(function (b) {
            var div = document.createElement('div');
            div.className = 'bairro-item';
            div.innerHTML =
                '<span class="bairro-item-nome">' + escHtml(b.nome) + '</span>'
                + '<button type="button" class="bairro-btn" data-id="' + b.id + '" data-nome="' + escHtml(b.nome) + '">Editar</button>'
                + '<button type="button" class="bairro-btn bairro-btn-del" data-id="' + b.id + '">Excluir</button>';
            container.appendChild(div);
        });

        container.querySelectorAll('.bairro-btn:not(.bairro-btn-del)').forEach(function (btn) {
            btn.addEventListener('click', function () { editBairro(btn.dataset.id, btn.dataset.nome); });
        });
        container.querySelectorAll('.bairro-btn-del').forEach(function (btn) {
            btn.addEventListener('click', function () { deleteBairro(btn.dataset.id); });
        });
    }

    window.openBairrosModal = function () {
        cancelBairroEdit();
        document.getElementById('err-bairro-nome').textContent = '';
        document.getElementById('bairros-list').innerHTML = '<div style="color:var(--ink-dim);font-size:0.8rem;text-align:center;padding:1rem">Carregando…</div>';
        bairrosModal.style.display = 'flex';
        loadBairros(renderBairrosList);
    };

    window.closeBairrosModal = function () {
        bairrosModal.style.display = 'none';
        // Atualiza o select do modal de sensor se estiver visível
        if (sensorModal.style.display === 'flex') {
            populateBairroSelect(document.getElementById('field-bairro-id').value);
        }
    };

    bairrosModal.addEventListener('click', function (e) { if (e.target === bairrosModal) closeBairrosModal(); });

    function editBairro(id, nome) {
        document.getElementById('bairro-edit-id').value = id;
        document.getElementById('field-bairro-nome').value = nome;
        document.getElementById('bairro-form-label').textContent = 'Editar bairro';
        document.getElementById('bairro-cancel-edit').style.display = 'inline-block';
        document.getElementById('field-bairro-nome').focus();
        document.getElementById('err-bairro-nome').textContent = '';
    }

    window.cancelBairroEdit = function () {
        document.getElementById('bairro-edit-id').value = '';
        document.getElementById('field-bairro-nome').value = '';
        document.getElementById('bairro-form-label').textContent = 'Novo bairro';
        document.getElementById('bairro-cancel-edit').style.display = 'none';
        document.getElementById('err-bairro-nome').textContent = '';
    };

    window.saveBairro = function () {
        var id   = document.getElementById('bairro-edit-id').value;
        var nome = document.getElementById('field-bairro-nome').value.trim();
        document.getElementById('err-bairro-nome').textContent = '';

        if (!nome) { document.getElementById('err-bairro-nome').textContent = 'Informe o nome do bairro.'; return; }

        fetch(id ? '/api/bairros/' + id : '/api/bairros', {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body: JSON.stringify({ nome: nome })
        })
        .then(function (r) { return r.json().then(function (b) { return { status: r.status, body: b }; }); })
        .then(function (r) {
            if (r.status === 422) {
                var errs = r.body.errors || {};
                document.getElementById('err-bairro-nome').textContent = (errs.nome && errs.nome[0]) || 'Erro de validação.';
                return;
            }
            if (r.status >= 400) { document.getElementById('err-bairro-nome').textContent = r.body.message || 'Erro.'; return; }
            cancelBairroEdit();
            loadBairros(renderBairrosList);
        });
    };

    function deleteBairro(id) {
        if (!confirm('Excluir este bairro? Os sensores vinculados ficarão sem bairro.')) return;
        fetch('/api/bairros/' + id, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        })
        .then(function (r) {
            if (r.status === 204) loadBairros(renderBairrosList);
        });
    }

    // Pré-carrega bairros para o select (silencioso, sem abrir modal)
    loadBairros(function () {});

})();
</script>
@endpush
