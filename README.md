# 🌧️ Drena Aí

**Monitoramento inteligente de drenagem urbana em tempo real.**

> Antecipando alagamentos antes que aconteçam.

---

## Sobre o projeto

**Drena Aí** é uma plataforma inteligente desenvolvida para auxiliar prefeituras na prevenção de alagamentos urbanos por meio do monitoramento contínuo da infraestrutura de drenagem da cidade.

A solução integra sensores instalados em bueiros e galerias subterrâneas para acompanhar condições críticas em tempo real e gerar alertas preventivos para equipes operacionais.

O objetivo é transformar dados em ação rápida, reduzindo impactos causados por chuvas intensas e obstruções no sistema de escoamento.

---

## Problema

Alagamentos repentinos em regiões urbanas normalmente ocorrem por uma combinação de fatores:

* Acúmulo de sedimentos e resíduos nos bueiros;
* Chuvas intensas em curto período;
* Sobrecarga das galerias subterrâneas.

Na maioria dos cenários, as equipes só atuam após o problema acontecer.

O **Drena Aí** muda esse modelo para atuação preventiva.

---

## Como funciona

Sensores distribuídos pela cidade realizam leitura contínua de três variáveis principais:

### 🟡 Percentual de obstrução

Mede o nível de bloqueio causado por sedimentos e resíduos.

Faixa monitorada:
`0% → totalmente livre`
`100% → totalmente obstruído`

---

### 🌧️ Índice pluviométrico instantâneo

Captura intensidade da chuva em tempo real.

Unidade:
`mm de precipitação`

---

### 🌊 Volume de vazão das galerias

Monitora a quantidade de água circulando no sistema subterrâneo.

Unidade:
`Litros por segundo (L/s)`

---

## Inteligência operacional

Com base na combinação dos indicadores, o sistema:

* Identifica pontos críticos antes do transbordamento;
* Emite alertas automáticos;
* Prioriza equipes de manutenção;
* Gera mapa operacional da cidade;
* Cria histórico para planejamento urbano.

---

## Funcionalidades

### Dashboard em tempo real

Visualização da cidade por regiões com status operacional.

### Central de alertas

Classificação automática:

🟢 Normal
🟡 Atenção
🟠 Risco
🔴 Emergência

### Histórico e indicadores

Análise de eventos anteriores e comportamento sazonal.

### Gestão de manutenção

Abertura e acompanhamento de intervenções.

### Relatórios executivos

Indicadores para tomada de decisão e prestação de contas.

---

## Exemplo de fluxo

```text
Sensor detecta aumento de chuva
          ↓
Obstrução cresce acima do limite
          ↓
Vazão começa a reduzir
          ↓
Drena Aí calcula risco
          ↓
Alerta enviado à operação
          ↓
Equipe atua antes do alagamento
```

---

## Benefícios

✔ Redução de alagamentos urbanos
✔ Resposta operacional mais rápida
✔ Menor custo corretivo
✔ Planejamento baseado em dados
✔ Mais segurança para a população

---

## Público-alvo

* Prefeituras
* Secretarias de Obras
* Defesa Civil
* Concessionárias de drenagem
* Centros de Operações Urbanas

---

## Stack (conceitual)

Frontend: Web Dashboard + Aplicativo
Backend: APIs + Processamento em tempo real
Sensoriamento: IoT
Infraestrutura: Cloud + Banco de dados temporal

---

## Slogan

**Drena Aí**
*Monitorar. Antecipar. Agir.*

---

## Tutorial de instalação (Laravel)

### Pré-requisitos

* PHP >= 8.1
* Composer
* Node.js e NPM
* Banco de dados (MySQL, PostgreSQL ou SQLite)
* Git

### Passo 1 — Clonar o repositório

```bash
git clone https://github.com/seu-usuario/drena-ai.git
cd drena-ai
```

### Passo 2 — Instalar dependências PHP

```bash
composer install
```

### Passo 3 — Instalar dependências front-end

```bash
npm install
```

### Passo 4 — Configurar o ambiente

```bash
cp .env.example .env
```

Edite o arquivo `.env` e configure as credenciais do banco de dados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=drena_ai
DB_USERNAME=root
DB_PASSWORD=
```

### Passo 5 — Gerar a chave da aplicação

```bash
php artisan key:generate
```

### Passo 6 — Executar as migrations

```bash
php artisan migrate
```

Se desejar popular o banco com dados de exemplo:

```bash
php artisan db:seed
```

### Passo 7 — Criar link simbólico de storage

```bash
php artisan storage:link
```

### Passo 8 — Compilar os assets

```bash
npm run dev
```

Para build de produção:

```bash
npm run build
```

### Passo 9 — Iniciar o servidor local

```bash
php artisan serve
```

A aplicação estará disponível em:
`http://localhost:8000`

### Passo 10 (opcional) — Filas e jobs em tempo real

Se o projeto utilizar filas para processamento de alertas e leituras de sensores:

```bash
php artisan queue:work
```

### Passo 11 (opcional) — Broadcast em tempo real

Caso o monitoramento em tempo real utilize WebSockets (Laravel Echo / Pusher / Soketi):

```bash
php artisan reverb:start
```

---

### Checklist rápido

* [ ] `.env` configurado
* [ ] Banco de dados criado
* [ ] Migrations executadas
* [ ] Assets compilados
* [ ] Servidor rodando

Pronto! O **Drena Aí** estará disponível para uso em ambiente de desenvolvimento. 🌧️