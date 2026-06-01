# Diario da Migracao

## 2026-06-01

- Revisado o objetivo da migracao para PHP 8, Clean Code e PSR.
- Transformado `TODO-PHP8-PSR.md` em checklist por fases.
- Atualizado `README.md` com estado atual da migracao e informacoes pendentes.
- Registrado que nao ha documentacao externa confiavel do legado alem dos arquivos do repositorio.
- Definido que o alvo e PHP 8.x.
- Definido que o banco novo sera PostgreSQL em Docker.
- Definido que `vendor/` sera controlado pelo Composer.
- Inferida a relacao entre `WebSocialSaude` e `WebSocialComum`.
- Inferido o caminho legado principal de configuracao de banco: `WebSocialComum/library/conf/dbConfig.xml`.
- Revisado `composer.lock`: as dependencias diretas travadas incluem PHP CS Fixer, PHPStan, PHPUnit e PHP_CodeSniffer.
- Revisado `.gitignore` para ignorar `vendor/`, caches, logs e artefatos gerados.
