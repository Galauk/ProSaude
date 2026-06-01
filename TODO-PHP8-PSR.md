# TODO - Migracao para PHP 8 / Clean Code / PSR

## Objetivo

Atualizar o projeto `SocialSaude` / `WebSocialComum` para PHP 8, adotando Clean Code e padroes PSR de forma progressiva, sem tentar reescrever todo o legado de uma vez.

O foco da migracao e:

- [ ] manter o sistema legado funcionando durante a transicao;
- [ ] criar uma fundacao moderna em `src/`;
- [ ] reduzir riscos criticos de seguranca;
- [ ] migrar regras e modulos aos poucos;
- [ ] aplicar PSR-1, PSR-4 e PSR-12 nos arquivos novos e refatorados;
- [ ] criar testes para proteger os fluxos migrados.

---

## Fase 0 - Inventario e controle inicial

### Informacoes que precisam ser confirmadas

- [x] Confirmar que nao ha mais documentacao externa do sistema legado alem dos arquivos do repositorio.
- [x] Definir PHP 8.x como alvo da migracao.
- [x] Definir PostgreSQL em Docker como banco do ambiente novo.
- [x] Definir que `vendor/` sera controlado pelo Composer e nao versionado.
- [ ] Definir versao exata do PHP 8.x para Docker/local.
- [ ] Definir versao exata do PostgreSQL para Docker.
- [ ] Definir encoding inicial do banco Docker, preferencialmente `UTF8` salvo incompatibilidade com dados legados.
- [x] Inferir a estrutura real entre `WebSocialSaude` e `WebSocialComum` a partir dos includes e caminhos existentes.
- [x] Identificar o caminho legado principal do arquivo de configuracao de banco.
- [ ] Quais diretorios sao codigo proprio.
- [ ] Quais diretorios sao bibliotecas de terceiros vendorizadas.
- [ ] Se bibliotecas legadas internas devem permanecer no repositorio.
- [ ] Se havera banco de teste ou base sanitizada para testes de integracao.
- [ ] Como sera feita a migracao gradual de senhas antigas em `MD5`.
- [ ] Quais modulos sao mais criticos para operacao diaria.
- [ ] Quais modulos podem ser migrados primeiro com menor risco.

### Diretriz quando faltar informacao do legado

- [x] Tratar o repositorio como fonte primaria da verdade.
- [ ] Registrar hipoteses tecnicas antes de implementar alteracoes estruturais.
- [ ] Validar hipoteses por leitura de codigo, execucao local e testes de caracterizacao.
- [ ] Evitar remover comportamento antigo sem antes identificar quem usa o fluxo.
- [ ] Preferir compatibilidade gradual a reescrita direta.

### Descobertas ja inferidas pelo codigo

- [x] Estrutura esperada do legado: `DOCUMENT_ROOT/WebSocialSaude` e `DOCUMENT_ROOT/WebSocialComum`.
- [x] `global.php` define `COMUM` como pasta irma `WebSocialComum`.
- [x] `auth.php` e `index.php` definem `$_SESSION['comum'] = "WebSocialComum/"`.
- [x] Caminho principal de configuracao de banco: `WebSocialComum/library/conf/dbConfig.xml`.
- [x] `api/db.inc.painel.php`, `sessao_controller.php` e `zf/application/configs/application.ini` referenciam `dbConfig.xml`.
- [x] Existem conexoes hardcoded antigas em funcoes de debug `vSQL()`, que devem ser removidas ou isoladas.

### Documentacao da migracao

- [ ] Criar `docs/migracao/DECISOES.md`.
- [ ] Criar `docs/migracao/DESCOBERTAS-LEGADO.md`.
- [ ] Criar `docs/migracao/DIARIO.md`.
- [ ] Registrar decisoes de ambiente, banco, Composer e arquitetura em `DECISOES.md`.
- [ ] Registrar fatos inferidos do legado em `DESCOBERTAS-LEGADO.md`.
- [ ] Registrar cada ciclo de trabalho em `DIARIO.md`.

### Diagnostico do estado atual

- [x] Identificar que o projeto e um legado PHP procedural com PostgreSQL e muitos arquivos na raiz.
- [x] Identificar dependencia externa/legada com `WebSocialComum`.
- [x] Identificar uso extenso de `pg_query()`.
- [x] Identificar presenca de Zend Framework antigo em `zf/`.
- [x] Identificar que `composer.json` foi criado como ponto de partida da migracao.
- [ ] Documentar as informacoes confirmadas acima.
- [ ] Mapear dependencias externas obrigatorias para rodar o sistema localmente.

### Git e organizacao do repositorio

- [x] Criar ou revisar `.gitignore`.
- [x] Decidir se `vendor/` deve ficar versionado ou ser instalado via Composer.
- [ ] Decidir se bibliotecas legadas internas devem permanecer no repositorio.
- [x] Adicionar `composer.lock` depois da primeira instalacao controlada das dependencias.
- [ ] Definir `license` no `composer.json` ou registrar como `proprietary`.
- [ ] Revisar requisito PHP do `composer.json`: o lock atual usa PHPUnit 10, que exige PHP 8.1+.
- [ ] Separar alteracoes de infraestrutura em commits pequenos.

---

## Fase 1 - Pacote zero da fundacao moderna

### Composer e autoload

- [x] Criar `composer.json` na raiz do projeto.
- [ ] Revisar requisito minimo de PHP no `composer.json` para PHP 8.x.
- [ ] Definir autoload PSR-4 para `App\\` apontando para `src/`.
- [ ] Definir autoload de testes para `App\\Tests\\` apontando para `tests/`.
- [ ] Rodar `composer install`.
- [ ] Validar `composer dump-autoload`.

### Ferramentas de qualidade

- [ ] Adicionar `phpcs.xml` com PSR-12 para `src/` e `tests/`.
- [ ] Adicionar `phpstan.neon` com nivel inicial realista.
- [ ] Adicionar configuracao do `php-cs-fixer`.
- [ ] Adicionar PHPUnit ou Pest para testes novos.
- [ ] Criar scripts Composer:
  - [ ] `composer cs`
  - [ ] `composer fix`
  - [ ] `composer analyse`
  - [ ] `composer test`
- [ ] Rodar as ferramentas apenas em `src/` e `tests/` no inicio, evitando aplicar PSR no legado inteiro de uma vez.

### Estrutura inicial recomendada

- [ ] Criar `src/`.
- [ ] Criar `tests/`.
- [ ] Criar estrutura base:

```text
src/
  Core/
    Auth/
    Config/
    Database/
    Http/
    Session/
  Shared/
    Exception/
    Support/
  Modules/
    Usuario/
    Paciente/
    Agendamento/
    Atendimento/
    Farmacia/
```

- [ ] Adotar `declare(strict_types=1);` em todos os novos arquivos PHP.
- [ ] Usar namespaces PSR-4 nos novos arquivos.
- [ ] Evitar dependencias diretas de `$_SESSION`, `$_GET`, `$_POST` e `$_SERVER` dentro de classes de dominio.

---

## Fase 2 - Bootstrap, paths e configuracao

### Bootstrap unico

- [ ] Criar um bootstrap moderno para carregar Composer, configuracao e constantes de caminho.
- [ ] Definir constantes de caminho:
  - [ ] `SOCIAL_ROOT`
  - [ ] `COMUM_ROOT`
  - [ ] `SAUDE_ROOT`
  - [ ] `APP_ROOT`
- [ ] Reduzir uso de `$_SESSION['root']`, `$_SESSION['modulo']` e `$_SESSION['comum']` para paths de include.
- [ ] Garantir que `session_start()` ocorra de forma controlada e apenas quando necessario.

### Arquivos prioritarios

- [ ] `global.php`
  - [ ] Substituir `__autoload()` por `spl_autoload_register()`.
  - [ ] Remover definicoes globais diretas quando houver alternativa segura.
  - [ ] Conectar o arquivo ao bootstrap moderno gradualmente.

- [ ] `config.inc.php`
  - [ ] Consolidar configuracoes em classe/array de configuracao.
  - [ ] Separar configuracao de ambiente, banco, caminhos e constantes de negocio.

- [ ] `sessao_controller.php`
  - [ ] Isolar leitura de configuracao do banco.
  - [ ] Evitar abrir nova conexao manual em cada instancia.
  - [ ] Centralizar politica de expiracao da sessao.

---

## Fase 3 - Banco de dados e camada de compatibilidade

### Nova camada de banco

- [ ] Criar `App\Core\Database\PgConnection`.
- [ ] Criar `App\Core\Database\PgDatabase`.
- [ ] Suportar queries parametrizadas.
- [ ] Padronizar tratamento de erro sem `die()`.
- [ ] Remover uso de `@` para supressao de erro nos novos componentes.
- [ ] Padronizar client encoding.
- [ ] Criar excecoes especificas para erro de conexao e erro de query.

### Compatibilidade com legado

- [ ] Manter `funcoes.db.php` funcionando durante a migracao.
- [ ] Fazer `funcoes.db.php` delegar gradualmente para a nova camada de banco.
- [ ] Preservar assinatura de funcoes usadas pelo legado quando necessario:
  - [ ] `db_query()`
  - [ ] `db_getRow()`
  - [ ] `db_get()`
  - [ ] `db_parseFirstCommand()`
- [ ] Trocar SQL concatenado por prepared statements primeiro nos fluxos criticos.

### Arquivos prioritarios

- [ ] `funcoes.db.php`
  - [ ] Mover logica de banco para classe `PgDatabase`.
  - [ ] Remover supressao com `@`.
  - [ ] Evitar `pg_query()` com SQL interpolado.
  - [ ] Trocar `die()` por excecao ou resposta controlada.

---

## Fase 4 - Compatibilidade obrigatoria com PHP 8

### Funcoes removidas ou perigosas

- [ ] Substituir `session_register()` por `$_SESSION[...]`.
  - [ ] `auth_pass.Val.php`
  - [ ] `auth_pass.Erro.php`

- [ ] Remover `get_magic_quotes_gpc()`.
  - [ ] `app-saude-profissional.php`
  - [ ] `app-saude-cidadao.php`

- [ ] Substituir `split()` por `explode()` ou `preg_split()`.
  - [ ] `calendario.inc.php`
  - [ ] `apac_print_sesgunda_via.php`
  - [ ] `apac_print_.php`
  - [ ] `hiperdia/geralHiperdia.php`
  - [ ] Varrer demais ocorrencias.

- [ ] Substituir `ereg()` e `ereg_replace()` por `preg_match()` e `preg_replace()`.
  - [ ] `e-sus-on/ThriftCidadao.php`
  - [ ] `e-sus/ThriftCidadao.php`
  - [ ] Varrer demais ocorrencias.

- [ ] Substituir `create_function()` por closures ou funcoes nomeadas.
- [ ] Substituir `preg_replace(..., '...e...')` por `preg_replace_callback()`.
- [ ] Mapear todos os usos de `eval()`.
- [ ] Substituir `eval()` por parse seguro, JSON, whitelist ou logica explicita.

### Sintaxe e compatibilidade

- [ ] Converter `<?` para `<?php` onde possivel.
- [ ] Revisar `<?=` e garantir que seja usado apenas para saida segura.
- [ ] Corrigir indices de array sem aspas, exemplo: `$_SESSION[root]`.
- [ ] Corrigir acessos a variaveis indefinidas que viram warnings/notices relevantes.
- [ ] Revisar funcoes que recebem `null` em parametros que nao aceitam mais `null` no PHP 8.
- [ ] Revisar construtores antigos com nome da classe.
- [ ] Revisar bibliotecas antigas incompatíveis com PHP 8.

### Arquivos ja mapeados com risco

- [ ] `agendar_exame.php`
- [ ] `medico.php`
- [ ] `exame/buscaDatasDisponiveis.php`
- [ ] `cad_lista_espera.php`
- [ ] `fazer_agendamento.js.php`
- [ ] `exame/exa_agendamento.js.php`
- [ ] `farmacia/far_agendamento.js.php`
- [ ] `app-saude-cidadao.php`
- [ ] `app-saude-profissional.php`

---

## Fase 5 - Seguranca critica antes de refatoracao visual

### SQL Injection

- [ ] Priorizar arquivos de login, API e escrita no banco.
- [ ] Substituir SQL interpolado por prepared statements.
- [ ] Remover uso de `addslashes()` como defesa de SQL.
- [ ] Validar entrada por tipo antes de consultar o banco.

### Autenticacao e senha

- [ ] Revisar `auth.php`.
- [ ] Revisar `auth_pass.php`.
- [ ] Revisar `authlib.inc.php`.
- [ ] Revisar `api/login.php`.
- [ ] Planejar migracao de senha `MD5` para `password_hash()` / `password_verify()`.
- [ ] Criar estrategia de migracao gradual de senhas no login.
- [ ] Evitar retornar dados sensiveis em APIs.

### Sessao e permissao

- [ ] Centralizar sessao em componente novo.
- [ ] Validar renovacao de sessao.
- [ ] Revisar tabela/fluxo `logon`.
- [ ] Revisar permissoes por arquivo/programa.
- [ ] Evitar confiar em `id_login` vindo por query string.
- [ ] Padronizar logout e expiracao.

### API e HTTP

- [ ] Revisar CORS aberto em `api/header.php`.
- [ ] Definir origens permitidas por configuracao.
- [ ] Padronizar respostas JSON.
- [ ] Padronizar status HTTP.
- [ ] Validar payload JSON e parametros de query.
- [ ] Criar camada de Request/Response para novas APIs.

### Saida HTML

- [ ] Escapar saida HTML com `htmlspecialchars()` ou view helpers.
- [ ] Adicionar CSRF tokens em formularios criticos.
- [ ] Evitar imprimir SQL/erros internos para usuario final.

---

## Fase 6 - Clean Code e PSR aplicados de forma incremental

### Padroes para codigo novo

- [ ] Usar PSR-12 em todo arquivo novo.
- [ ] Usar PSR-4 e namespace em toda classe nova.
- [ ] Usar nomes claros para classes, metodos e variaveis.
- [ ] Evitar metodos longos.
- [ ] Separar regra de negocio, acesso a dados e apresentacao.
- [ ] Preferir injecao de dependencia a `global`.
- [ ] Evitar acesso direto a superglobais dentro de servicos.

### Refatoracao gradual do legado

- [ ] Extrair funcoes de `funcoes.inc.php` para servicos quando forem tocadas.
- [ ] Extrair HTML grande para views/templates quando o modulo for migrado.
- [ ] Extrair JavaScript inline para arquivos proprios quando o modulo for migrado.
- [ ] Trocar includes dinamicos por dependencias explicitas.
- [ ] Evitar mudar comportamento junto com refatoracao estrutural, exceto em correcao de bug/seguranca.

---

## Fase 7 - Testes e protecao contra regressao

### Testes iniciais

- [ ] Criar estrutura `tests/`.
- [ ] Criar teste para carregamento do autoload.
- [ ] Criar teste para leitura de configuracao.
- [ ] Criar teste para montagem de conexao PostgreSQL sem expor senha.
- [ ] Criar testes unitarios para helpers novos.

### Testes de caracterizacao do legado

- [ ] Mapear comportamento atual de login.
- [ ] Mapear comportamento atual de sessao expirada.
- [ ] Mapear comportamento atual de permissao por usuario.
- [ ] Mapear comportamento atual de uma consulta simples de paciente.
- [ ] Mapear comportamento atual de agendamento basico.

### Testes de integracao

- [ ] Definir banco de teste ou estrategia de fixture.
- [ ] Garantir que testes nao rodem contra banco de producao.
- [ ] Criar testes para novos repositorios/classes de banco.
- [ ] Criar testes para API modernizada.

---

## Fase 8 - Migração modular

### Ordem sugerida

- [ ] Infraestrutura: configuracao, bootstrap, banco, sessao.
- [ ] Login e autenticacao.
- [ ] Permissoes.
- [ ] API.
- [ ] Agendamento.
- [ ] Paciente.
- [ ] Farmacia e estoque.
- [ ] Atendimento.
- [ ] Prontuario.
- [ ] Relatorios e impressoes.
- [ ] E-SUS / exportacoes.

### Criterio para migrar um modulo

Para cada modulo escolhido:

- [ ] Mapear arquivos envolvidos.
- [ ] Mapar tabelas principais usadas.
- [ ] Identificar entradas `GET`, `POST` e JSON.
- [ ] Identificar queries de leitura.
- [ ] Identificar queries de escrita.
- [ ] Criar testes de caracterizacao quando possivel.
- [ ] Extrair acesso a banco para classe/repository.
- [ ] Extrair regra de negocio para service.
- [ ] Centralizar validacao de entrada.
- [ ] Escapar saida HTML.
- [ ] Remover SQL interpolado.
- [ ] Rodar testes e analise estatica no escopo migrado.

---

## Fase 9 - Arquivos de maior impacto para refatoracao inicial

### Bootstrap e infraestrutura

- [ ] `global.php`
- [ ] `funcoes.db.php`
- [ ] `funcoes.inc.php`
- [ ] `config.inc.php`
- [ ] `sessao_controller.php`

### Login, sessao e permissao

- [ ] `auth.php`
- [ ] `auth_pass.php`
- [ ] `auth_pass.Val.php`
- [ ] `auth_pass.Erro.php`
- [ ] `authlib.inc.php`
- [ ] `api/login.php`

### Compatibilidade PHP 8

- [ ] `calendario.inc.php`
- [ ] `apac_print_sesgunda_via.php`
- [ ] `apac_print_.php`
- [ ] `e-sus-on/ThriftCidadao.php`
- [ ] `e-sus/ThriftCidadao.php`
- [ ] `hiperdia/geralHiperdia.php`

### Risco por `eval()` ou logica dinamica

- [ ] `agendar_exame.php`
- [ ] `medico.php`
- [ ] `exame/buscaDatasDisponiveis.php`
- [ ] `cad_lista_espera.php`
- [ ] `fazer_agendamento.js.php`
- [ ] `exame/exa_agendamento.js.php`
- [ ] `farmacia/far_agendamento.js.php`

### Apps externos/cidadao/profissional

- [ ] `app-saude-cidadao.php`
- [ ] `app-saude-profissional.php`

---

## Fase 10 - Documentacao operacional

- [ ] Atualizar README com requisitos reais.
- [ ] Documentar instalacao local.
- [ ] Documentar dependencia com `WebSocialComum`.
- [ ] Documentar configuracao do banco.
- [ ] Documentar encoding esperado.
- [ ] Documentar como rodar Composer.
- [ ] Documentar como rodar testes.
- [ ] Documentar como rodar PHPCS/PHPStan.
- [ ] Documentar processo de migracao por modulo.
- [ ] Atualizar `docs/` com instrucoes compatíveis com PHP 8.

---

## Comandos de inspeção sugeridos

### Localizar funcoes removidas/perigosas

```bash
grep -R "session_register\|get_magic_quotes_gpc\|split(\|ereg_\|ereg(\|create_function\|eval(" .
```

### Rodar qualidade no codigo novo

```bash
composer cs
composer analyse
composer test
```

### Rodar ferramentas manualmente

```bash
phpcs --standard=PSR12 src tests
phpstan analyse src tests --level=5
vendor/bin/phpunit
```

---

## Regra de ouro da migracao

- [ ] Nao refatorar o projeto inteiro de uma vez.
- [ ] Nao misturar grande refatoracao estrutural com mudanca de regra de negocio.
- [ ] Priorizar seguranca e compatibilidade antes de limpeza estetica.
- [ ] Criar componentes novos em `src/` e conectar o legado a eles gradualmente.
- [ ] Migrar por modulo, com commits pequenos, testes e validacao manual do fluxo.
