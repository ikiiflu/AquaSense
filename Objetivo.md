Objetivos
• Projetar, normalizar, implementar e consumir um banco de dados relacional robusto.
• Criar um ecossistema completo que simula a ingestão de dados de sensores urbanos e os expõe em um
painel/dashboard analítico em tempo real, testando critérios práticos de adequabilidade técnica.
• Trabalhar em equipe para resolver problemas de banco de dados.
Cenário
A Secretaria de Infraestrutura Urbana de Caratinga sofre com alagamentos repentinos nas zonas de
escoamento central. Para sanar isso de maneira inteligente, (suponha que) bueiros da cidade receberam
sensores integrados capazes de monitorar três variáveis principais em tempo real: o percentual de obstrução por
sedimentos e lixo (0% a 100%), o índice pluviométrico instantâneo (precipitação em mm) e o volume de vazão
das galerias subterrâneas (L/s).
Problema
O desafio das equipes consiste em arquitetar o banco de dados que consolidará esses fluxos contínuos e
fornecerá consultas analíticas de alta performance para alimentar a tomada de decisão da equipe de engenharia
do município.
Definição do Esquema e Projeto Físico Inicial
O modelo lógico deve ser mapeado para garantir integridade referencial com remoções em cascata
(ON DELETE CASCADE) e tipos numéricos otimizados para coordenadas geoespaciais e métricas de
sensores.
Escopo do Projeto
Fase 1: Concepção
• Definição e mapeamento do Diagrama Entidade-Relacionamento (DER).
• Definição exata de tipos de chaves e dicionário de dados.
• Validação das Formas Normais (1FN, 2FN e 3FN).
• Expressões em Álgebra Relacional das principais visões analítica que devem ser obrigatoriamente
codificadas em SQL.
• Scripts DDL (comandos SQL) gerados e testados no SGBD.
Fase 2: Integração
• Desenvolvimento de um script backend (Python, PHP...) que simule a inserção automática e
contínua de registros fictícios na tabela de leituras dos sensores.
• Criação de endpoints de API para expor as tabelas e os cruzamentos de dados via JSON.
Fase 3: Dashboard
• Construção de uma interface visual (React, HTML/CSS...) alimentada pela API.
• Painel com alertas de bueiros críticos.
• Gráfico visual (linhas, barras ou pizza) consolidando dados por bairros.