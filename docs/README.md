# Documentacao

Esta pasta deve conter apenas documentacao util para a nova versao do projeto e referencias legadas que ajudem na migracao.

## Estrutura

- `migracao/`: documentacao ativa da migracao para PHP 8, Clean Code e PSR.
- `legado/`: artefatos historicos preservados somente quando ajudam a entender banco, regras antigas ou dependencias do sistema.

## O que foi retirado da documentacao ativa

Foram removidos lembretes antigos que nao servem para a nova versao, como instrucoes de IonCube/PHP 5, instalacao manual de Zend em XAMPP, troca de brasao, rotina manual de backup, notas de release antigas e dicas soltas de debug.

Quando algum arquivo antigo continha informacao util, o conteudo foi resumido em `migracao/DESCOBERTAS-LEGADO.md` ou preservado em `legado/`.

## Regra para novos documentos

- Documentos de decisao tecnica devem ir para `migracao/DECISOES.md`.
- Descobertas sobre o legado devem ir para `migracao/DESCOBERTAS-LEGADO.md`.
- Registro cronologico do trabalho deve ir para `migracao/DIARIO.md`.
- Scripts SQL antigos devem ficar em `legado/banco/` ate existir uma estrategia nova de migrations.
