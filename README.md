# SocialSaude / ProSaude

Sistema web em PHP para gestão de serviços de saúde pública. Este projeto foi desenvolvido como uma plataforma integrada para o SUS, com funcionalidades de atendimento, agendamento, prontuário eletrônico, farmácia, vacina, controle de estoque e relatórios.

## Visão geral

- Aplicação legacy em PHP com estrutura monolítica e muitos arquivos no diretório principal.
- Utiliza PostgreSQL no backend por meio de funções `pg_*`.
- Possui integração com módulos E-SUS e exportação de dados para sistemas de saúde.
- Inclui interfaces e recursos para cidadão, profissional e administração.
- Contém tanto código legado quanto partes em uma sub-aplicação `zf/` e módulos adicionais.

## Principais recursos

- Autenticação de usuários
- Agendamento de consultas e exames
- Atendimento ambulatorial e hospitalar
- Cadastro e consulta de pacientes
- Emissão de atestados, receitas e guias
- Controle de estoque, requisições e dispensação de medicamentos
- Gestão de vacinas e alertas de imunização
- Relatórios gerenciais e impressão de documentos
- Integração E-SUS / exportação de dados governamentais

## Objetivo da migração

Este repositório está em processo de atualização para:

- PHP 8
- práticas de código limpo (Clean Code)
- padrões PSR-1 / PSR-12
- autoload PSR-4 com pasta `src/`
- arquitetura mais modular e testável

O roteiro operacional da migração está documentado em [`TODO-PHP8-PSR.md`](TODO-PHP8-PSR.md). Ele deve ser usado como checklist principal antes de qualquer refatoração estrutural.

## Estado atual da migração

- O `composer.json` foi criado como ponto de partida para Composer, PSR-4 e ferramentas de qualidade.
- A pasta `src/` é o destino para código novo e componentes refatorados.
- O sistema legado deve continuar funcionando enquanto os módulos são migrados gradualmente.
- As primeiras prioridades são fundação PHP 8, bootstrap/configuração, camada de banco, segurança de login/API e compatibilidade com funções removidas.
- As ferramentas de qualidade devem começar analisando apenas `src/` e `tests/`, evitando aplicar PSR no legado inteiro de uma vez.
- O projeto original foi descontinuado; portanto, as decisões e regras do legado devem ser inferidas a partir dos arquivos existentes, banco de dados disponível e testes de caracterização.
- O alvo da migração é PHP 8.x.
- O novo ambiente de banco deve usar PostgreSQL em Docker.
- A pasta `vendor/` deve ser controlada pelo Composer e não deve ser versionada.

## Informações a confirmar antes da próxima fase

Antes de executar o pacote inicial da migração, confirmar:

- Versão exata do PHP 8.x para o ambiente Docker/local.
- Versão do PostgreSQL a usar no Docker.
- Encoding inicial do banco Docker: preferencialmente `UTF8`, salvo incompatibilidade com dados legados.
- Quais diretórios são código próprio e quais são bibliotecas de terceiros vendorizadas.
- Se haverá banco de teste ou base sanitizada para testes de integração.
- Como será feita a migração gradual de senhas antigas em `MD5`.

Como não há documentação externa do sistema legado, qualquer item não confirmável deve ser tratado como hipótese técnica e validado por leitura do código, execução local, banco de teste e testes de caracterização.

## Estrutura inferida do legado

Pelos includes e constantes encontrados no código, a estrutura esperada do legado é:

```text
DOCUMENT_ROOT/
  WebSocialSaude/
  WebSocialComum/
```

O projeto atual corresponde ao módulo `WebSocialSaude`. A pasta `WebSocialComum` é uma dependência legada externa/irmã, usada para funções comuns, autenticação, bibliotecas compartilhadas, assets e configuração de banco.

Referências encontradas:

- `global.php` define `COMUM` como `dirname(SOCIAL) . "/WebSocialComum/"`.
- `auth.php` e `index.php` definem `$_SESSION['comum'] = "WebSocialComum/"`.
- Diversos arquivos carregam assets por `/WebSocialComum/...`.
- `zf/application/configs/application.ini` aponta para `APPLICATION_PATH "/../../../WebSocialComum/library/conf/dbConfig.xml"`.

## Configuração de banco inferida

O caminho legado principal para configuração de banco é:

```text
WebSocialComum/library/conf/dbConfig.xml
```

Esse XML é lido por arquivos como `api/db.inc.painel.php`, `sessao_controller.php` e a aplicação Zend em `zf/application/configs/application.ini`. Os campos de conexão são armazenados em base64 no XML, com chaves como `host`, `dbname`, `user`, `porta` e `password`.

Também existem conexões hardcoded antigas em funções de debug, como `vSQL()` em alguns arquivos. Essas conexões devem ser tratadas como legado inseguro e removidas ou substituídas durante a migração.

## Como documentar a migração

A documentação deve ser mantida junto do código. O formato recomendado é:

- `README.md`: visão geral, decisões atuais e como começar.
- `TODO-PHP8-PSR.md`: checklist operacional da migração.
- `docs/migracao/DECISOES.md`: decisões técnicas tomadas, com contexto e consequência.
- `docs/migracao/DESCOBERTAS-LEGADO.md`: fatos inferidos por leitura do código.
- `docs/migracao/DIARIO.md`: registro cronológico curto do que foi feito em cada etapa.

Sempre que uma decisão mudar arquitetura, ambiente, banco, segurança ou compatibilidade, ela deve entrar em `docs/migracao/DECISOES.md`. Sempre que descobrirmos comportamento do legado, ele deve entrar em `docs/migracao/DESCOBERTAS-LEGADO.md`.

## Estrutura atual

- `src/` - local de destino para o novo código migrado
- `zf/` - sub-aplicação de framework Zend / MVC existente
- `e-sus/`, `e-sus-on/`, `exportacao_esus/` - módulos de integração E-SUS
- `api/` - APIs e rotas de integração
- `docs/` - documentação de instalação e atualização

## Notas importantes

- O projeto atual mistura lógica de apresentação e negócios em muitos arquivos PHP.
- A migração deve ser feita em fases: compatibilidade com PHP 8, refatoração de bootstrap e serviços, e depois adoção completa de PSR.
- Não refatorar todo o código de uma vez; mover e reescrever módulos aos poucos.

## Licença

Informação de licença não disponível no repositório. Consulte a equipe ou o mantenedor do sistema para confirmar os termos de uso.
