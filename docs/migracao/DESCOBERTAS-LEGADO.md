# Descobertas do Legado

Este arquivo registra fatos inferidos a partir do codigo existente.

## Estrutura entre WebSocialSaude e WebSocialComum

O codigo espera uma estrutura com duas pastas no mesmo nivel dentro do `DOCUMENT_ROOT`:

```text
DOCUMENT_ROOT/
  WebSocialSaude/
  WebSocialComum/
```

O repositorio atual corresponde ao modulo `WebSocialSaude`.

Referencias:

- `global.php` define `SOCIAL` e `SAUDE` como `dirname(__FILE__)`.
- `global.php` define `COMUM` como `dirname(SOCIAL) . DS . "WebSocialComum" . DS`.
- `global.php` define `LINKCOMUM` como `/WebSocialComum`.
- `auth.php`, `index.php`, `app-saude-cidadao.php` e `app-saude-profissional.php` definem ou usam `$_SESSION['comum'] = "WebSocialComum/"`.
- Muitos arquivos carregam assets diretamente de `/WebSocialComum/...`.

## Configuracao de banco

O caminho legado principal para configuracao de banco e:

```text
WebSocialComum/library/conf/dbConfig.xml
```

Referencias:

- `api/db.inc.painel.php` le `../../WebSocialComum/library/conf/dbConfig.xml`.
- `sessao_controller.php` le `__DIR__ . "/../WebSocialComum/library/conf/dbConfig.xml"`.
- `zf/application/configs/application.ini` define `WSResources.dbConfig = APPLICATION_PATH "/../../../WebSocialComum/library/conf/dbConfig.xml"`.

O XML contem campos codificados em base64, incluindo:

- `nome`
- `host`
- `dbname`
- `user`
- `porta`
- `password`

## Conexoes hardcoded antigas

Foram encontradas conexoes PostgreSQL hardcoded em funcoes de debug `vSQL()`:

- `funcoes.inc.php`
- `funcoes.incAgendamento.php`
- `funcoes_exa.inc.php`

Essas conexoes devem ser tratadas como legado inseguro e removidas, isoladas ou substituidas por configuracao centralizada durante a migracao.

## Observacoes funcionais preservadas de documentos antigos

- O modulo BPA usava uma sigla de municipio com 3 letras para gerar o "Relatorio de Controle de Remessa".
- O nome do municipio era salvo na tabela `secretaria`.
- Havia customizacao de brasao em caminhos do `WebSocialComum`, `WebSocialSaude/imgs/brasao.jpg` e `zf/public/images/brasao.jpg`.
- Documentos antigos mencionavam banco com encoding `WIN1252`, conversoes entre `UTF8`, `LATIN1` e `WIN1252`, e uso de `template0` ao criar bases restauradas.

Essas observacoes nao devem ser tratadas como requisitos definitivos da nova versao. Elas servem como hipoteses para validar quando os modulos correspondentes forem migrados.

## Documentos antigos retirados da documentacao ativa

Foram retirados da area ativa de `docs/` arquivos de lembrete sobre:

- instalacao de IonCube para PHP 5;
- instalacao manual de Zend em XAMPP/PEAR;
- rotina manual de backup com Java e arquivos `.bat`;
- substituicao manual de brasao;
- notas de release antigas;
- dicas soltas de debug JavaScript;
- instrucoes manuais de importacao/restauracao de banco.

Os scripts SQL historicos foram preservados em `docs/legado/banco/`.
