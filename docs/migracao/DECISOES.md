# Decisoes da Migracao

Este arquivo registra decisoes tecnicas tomadas durante a migracao para PHP 8, Clean Code e PSR.

## 2026-06-01 - Repositorio como fonte primaria

Contexto: o sistema legado foi descontinuado e nao ha documentacao externa confiavel alem dos arquivos existentes.

Decisao: o repositorio sera tratado como fonte primaria da verdade. Comportamentos e dependencias do legado devem ser inferidos por leitura do codigo, execucao local, banco de teste e testes de caracterizacao.

Consequencia: qualquer regra nao confirmada deve ser registrada como hipotese antes de alteracoes estruturais.

## 2026-06-01 - Alvo PHP 8.x

Contexto: a migracao tem como objetivo modernizar o projeto para PHP 8.

Decisao: o alvo da migracao sera PHP 8.x. A versao exata para Docker/local ainda deve ser definida.

Consequencia: as ferramentas e dependencias devem ser escolhidas considerando compatibilidade com PHP 8.x. O `composer.lock` atual instala PHPUnit 10, que exige PHP 8.1 ou superior.

## 2026-06-01 - PostgreSQL em Docker

Contexto: o novo ambiente de desenvolvimento/migracao deve usar banco reprodutivel.

Decisao: o banco novo sera PostgreSQL em Docker.

Consequencia: sera necessario criar configuracao Docker e definir versao exata do PostgreSQL, encoding inicial e estrategia para carga de dados/testes.

## 2026-06-01 - Vendor controlado pelo Composer

Contexto: o projeto passou a usar Composer para dependencias modernas.

Decisao: a pasta `vendor/` sera gerada pelo Composer e nao sera versionada.

Consequencia: `composer.lock` deve ser versionado para manter instalacoes reprodutiveis, enquanto `/vendor/` deve permanecer no `.gitignore`.

## 2026-06-01 - Limpeza da pasta docs

Contexto: a pasta `docs/` continha lembretes antigos, instrucoes operacionais obsoletas e scripts SQL historicos misturados com a documentacao nova da migracao.

Decisao: manter em `docs/` apenas documentacao util para a nova versao e mover artefatos historicos de banco para `docs/legado/banco/`. Remover documentos soltos que dependiam de PHP 5, IonCube, XAMPP, rotinas manuais antigas ou customizacoes operacionais fora do escopo da nova arquitetura.

Consequencia: `docs/migracao/` passa a ser a documentacao ativa da migracao, `docs/legado/` guarda referencias historicas, e scripts SQL antigos nao devem ser tratados como migrations oficiais da nova versao.
