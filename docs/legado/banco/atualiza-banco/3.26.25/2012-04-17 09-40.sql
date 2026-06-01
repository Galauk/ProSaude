ALTER TABLE ofertas_solicitacoes ADD COLUMN for_codigo bigint;
INSERT INTO fornecedor (for_codigo,for_nome) VALUES(10000000,'ASSISTENCIA SOCIAL');
UPDATE ofertas_solicitacoes SET for_codigo = 10000000;

update permissoes set perm_descricao='Fornecedor',perm_programa='../WebSocialComum/fornecedor.php',perm_objeto='Fornecedor' where perm_codigo='30';
