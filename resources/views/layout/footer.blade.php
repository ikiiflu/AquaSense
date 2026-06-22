<footer class="app-statusbar" role="contentinfo">
    <div class="statusbar-left">
        <div class="statusbar-item">
            <div class="statusbar-dot is-live" aria-hidden="true"></div>
            <span class="statusbar-label">Sistema</span>
            <span class="statusbar-value" style="color:var(--status-ok)">Operacional</span>
        </div>
        <div class="statusbar-divider" aria-hidden="true"></div>
        <div class="statusbar-item">
            <span class="statusbar-label">Sensores online</span>
            <span class="statusbar-value">{{ $footerActiveSensors ?? '-' }}/{{ $footerTotalSensors ?? '-' }}</span>
        </div>
        <div class="statusbar-divider" aria-hidden="true"></div>
        <div class="statusbar-item">
            <span class="statusbar-label">Última sinc.</span>
            <span class="statusbar-value">
                @if($footerLastSync)
                    {{ $footerLastSync->diffForHumans() }}
                @else
                    Sem dados
                @endif
            </span>
        </div>
    </div>
    <div class="statusbar-right">
        @if(\App\Models\Setting::get('modo_atualizacao', 'manual') === 'automatico')
        <div class="statusbar-item">
            <span class="statusbar-label">Próx. atualização</span>
            <span class="statusbar-value" id="next-refresh-countdown" style="color:var(--flow)">--</span>
        </div>
        <div class="statusbar-divider" aria-hidden="true"></div>
        @endif
        <div class="statusbar-item">
            <span class="statusbar-label">Hora local</span>
            <span class="statusbar-value" id="statusbar-clock">--:--</span>
        </div>
    </div>
</footer>
