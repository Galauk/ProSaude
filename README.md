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
