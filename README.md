# AquaSense

Sistema de monitoramento simulado de drenagem urbana em tempo real.

Desenvolvido como projeto acadêmico para demonstrar o uso de sensores em bueiros urbanos, geração automática de leituras, alertas por nível de obstrução e visualização georreferenciada.

---

## Funcionalidades

- **Dashboard** — métricas ao vivo (obstrução média, precipitação, vazão) e alertas ativos
- **Mapa operacional** — sensores georreferenciados com status colorido via MapLibre GL JS
- **Histórico de leituras** — tabela paginada com filtro por sensor
- **Gráficos** — obstrução e precipitação por sensor e por bairro
- **Central de alertas** — classificação automática em Atenção / Risco / Crítico
- **Configurações** — intervalo de leitura, modo de simulação climática, limiares de alerta
- **Log SQL** — registro dos últimos comandos executados no banco

---

## Stack

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 13, PHP 8.3+ |
| Banco de dados | MySQL 8.4 |
| Templates | Blade (sem Vite, sem npm) |
| Frontend | CSS puro + JavaScript vanilla |
| Mapas | MapLibre GL JS |
| Agendamento | Laravel Scheduler + cron do servidor |

---

## Instalação local

**Pré-requisitos:** PHP 8.3+, Composer, MySQL.

```bash
git clone https://github.com/ikiiflu/Drena-Ai.git aquasense
cd aquasense

composer install

cp .env.example .env
```

Edite o `.env` com os dados do banco:

```env
DB_DATABASE=aquasense
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Acesse `http://localhost` (ou o virtual host configurado).

**Login padrão:** `admin@aquasense.com` / `password`
> Senha provisória de desenvolvimento.

---

## Geração automática de leituras

As leituras são geradas pelo servidor via Laravel Scheduler, sem depender do navegador.

**Em desenvolvimento** — rodar manualmente em um terminal separado:

```bash
php artisan sensor:simulate --loop=auto
```

**Em produção** — adicionar ao cron do servidor (uma vez):

```
* * * * * /usr/bin/php /caminho/para/aquasense/artisan schedule:run >> /dev/null 2>&1
```

O intervalo entre leituras é configurável em **Configurações → Intervalo de leitura**.

---

## Modos de simulação

Configurável em tempo real pela interface:

| Modo | Comportamento |
|---|---|
| `sem_chuva` | Precipitação mínima, obstrução tende a cair |
| `normal` | Chuva simulada entre 14h–18h |
| `chuva_fraca` | Precipitação leve, leve tendência de subida |
| `chuva_forte` | Precipitação moderada, obstrução sobe |
| `tempestade` | Precipitação intensa, obstrução sobe rapidamente |

---

## Níveis de alerta

| Nível | Obstrução padrão |
|---|---|
| Atenção | ≥ 10% |
| Risco | ≥ 40% |
| Crítico | ≥ 70% |

Os limiares são configuráveis em **Configurações**.
