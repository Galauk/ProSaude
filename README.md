# ProSaude 

Sistema integrado de gestão de saúde pública, desenvolvido para atender as necessidades do **SUS** (Sistema Único de Saúde) em municípios brasileiros.

## Sobre o Projeto

**ProSaude** é um sistema completo para Unidades Básicas de Saúde (UBS), secretarias municipais de saúde e prestadores de serviço ao SUS.  
O projeto está em **processo ativo de modernização** (PHP 8 + PSR).

### Principais Funcionalidades
- Agendamento de consultas e exames
- Prontuário eletrônico do paciente
- Atendimento ambulatorial, hospitalar e em grupo
- Controle de farmácia e dispensação
- Vacinação e alertas de imunização
- Exames laboratoriais e imagem
- Módulos específicos (Pré-natal, Hiperdia, Hanseníase, Tuberculose, etc.)
- Integração com **e-SUS**
- Emissão de atestados, receitas, guias e etiquetas
- Relatórios gerenciais e totem/guichê

## Estado Atual da Migração (12/06/2026)

- **Fase atual**: Final da Fase 2 e início da Fase 3
- Fundação moderna (`src/`) já criada com PSR-4
- Router, Controllers, Views, SessionManager e Config via `.env`
- Limpeza significativa de código e assets legados
- Sistema legado ainda funciona normalmente

**Próximos passos prioritários:**
- Camada moderna de banco de dados (prepared statements)
- Migração de senhas MD5
- Eliminação de SQL Injection crítico

---

## Como Rodar (Atual)

1. Clone o repositório
2. Copie `.env.example` para `.env` e configure o banco
3. `composer install`
4. Acesse via `public/index.php` (entry point moderno)
5. O sistema legado continua acessível normalmente

**Requisitos:**
- PHP 8.1+
- PostgreSQL
- Extensão `pgsql`

---

## Estrutura de Pastas (Principais)

- `src/` → Código novo (moderna)
- `public/` → Entry point moderno
- `config/` → Configurações
- Pastas legadas (agendamento, farmacia, prontuario, etc.)
- `docs/` → Documentação da migração

---

## Documentação da Migração

- [TODO-PHP8-PSR.md](TODO-PHP8-PSR.md) → Plano detalhado de migração
- `docs/` → Decisões e descobertas do legado

## Contribuição

O projeto está aberto a contribuições, especialmente na modernização.  
Recomenda-se ler o `TODO-PHP8-PSR.md` antes de iniciar.

---

**Licença:** Proprietário / Uso restrito a projetos de saúde pública.