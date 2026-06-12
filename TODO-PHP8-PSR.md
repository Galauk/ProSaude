# TODO - Migração para PHP 8 / Clean Code / PSR
**Atualizado em: 12/06/2026**

## Objetivo
Atualizar o projeto **ProSaude / SocialSaude** para PHP 8, adotando Clean Code e padrões PSR de forma **progressiva**, mantendo o sistema legado funcionando durante toda a transição.

**Princípios da migração:**
- Manter o sistema legado 100% funcional
- Criar uma fundação moderna em `src/`
- Reduzir riscos críticos de segurança
- Migrar módulos aos poucos
- Aplicar PSR-1, PSR-4 e PSR-12 nos novos arquivos

---

## Progresso Atual (12/06/2026)

**Conquistas recentes:**
- `composer.json` + autoload PSR-4 configurado
- Pasta `src/` com estrutura moderna (`Core/`, `Models/`, `Repositories/`, `Controllers/`, `Routing/`, `Views/`)
- Suporte a `.env` + Config loader
- `SessionManager` e SessionMiddleware
- Router básico + Controllers + View system
- Models iniciais (`Usuario`, `Documento`, `Endereco`, etc.)
- Limpeza pesada de arquivos legados, imagens obsoletas e bibliotecas antigas
- Bootstrap moderno em `public/index.php`

**Fases concluídas / em andamento:**
- Fase 0 → **Concluída**
- Fase 1 → **Concluída**
- Fase 2 → **Bem avançada**
- Fase 3 → **Início**

---

## Fase 0 - Inventário e controle inicial
- [x] Maior parte das definições e descobertas concluídas
- [ ] Definir versão exata do PHP 8.x e PostgreSQL para Docker
- [ ] Estratégia completa de migração de senhas MD5

---

## Fase 1 - Fundação moderna (Pacote Zero)
**Status: Concluída**

- [x] `composer.json` + autoload PSR-4 (`App\` → `src/`)
- [x] Estrutura de pastas em `src/`
- [x] Ferramentas de qualidade (PHPCS, PHPStan, PHP-CS-Fixer, PHPUnit)
- [x] `declare(strict_types=1)` nos novos arquivos

---

## Fase 2 - Bootstrap, paths e configuração
**Status: Avançada**

- [x] Bootstrap moderno (`public/index.php`)
- [x] `.env` + Config loader
- [x] `SessionManager` + SessionMiddleware
- [x] Router simples, Controllers e sistema de Views
- [x] Helpers básicos
- [ ] Refatorar `global.php`, `config.inc.php` e integração completa com legado
- [ ] Definir constantes de caminho de forma centralizada

---

## Fase 3 - Banco de dados e camada de compatibilidade (Próxima prioridade)
**Status: Início**

- [x] Configuração básica e singleton de conexão
- [ ] Criar `App\Core\Database\PgConnection` e `PgDatabase` com prepared statements
- [ ] Tratamento adequado de erros (sem `die()` e `@`)
- [ ] Fazer `funcoes.db.php` delegar para a nova camada
- [ ] Exceções personalizadas

---

## Fase 4 - Compatibilidade obrigatória com PHP 8
- [ ] Substituir funções removidas (`session_register()`, `split()`, `ereg()`, etc.)
- [ ] Corrigir sintaxe incompatível em arquivos legados
- [ ] Testar todo o sistema em PHP 8

---

## Fase 5 - Segurança crítica (Paralela à Fase 3)
**Alta prioridade**
- [ ] Migrar autenticação e senhas MD5 → `password_hash()`
- [ ] Eliminar SQL Injection nos pontos críticos (login, agendamento, cadastro)
- [ ] CSRF, escaping de saída, sessão segura

---

## Próximas Fases (6 a 8)
- Clean Code incremental
- Testes (unitários + caracterização)
- Migração modular (ordem sugerida):
  1. Login / Autenticação
  2. Usuário e Permissões
  3. Cadastro de Paciente
  4. Agendamento
  5. Demais módulos

---

**Recomendação atual:**  
Concluir a **Fase 3 (Banco de Dados)** + iniciar a **Fase 5 (Segurança)**.