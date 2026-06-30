# Roadmap de Migração - ProSaude (Legacy → Moderno)

**Última atualização:** 29/06/2026  
**Estratégia:** Strangler Fig Pattern (migrar gradualmente mantendo o legado funcional)

## Princípios da Migração

- Manter o sistema 100% funcional durante toda a transição
- Usar o novo Router (`src/Routing`) como ponto de entrada principal
- Criar paralelamente novas rotas modernas
- Remover arquivos legados **somente** após o módulo estar estável em produção por pelo menos 30-60 dias

---

## Ordem Recomendada de Migração

### Fase 1 – Fundação (Concluída)
- Estrutura `src/`, Router, Controllers, Views, Session, .env
- Sistema de Migrations e Seeders

### Fase 2 – Autenticação e Usuários (Em andamento / Prioridade Alta)

**Módulo:** Autenticação + Gestão de Usuários

**Arquivos/Folders legados que poderão ser removidos após conclusão:**
- `auth.php`
- `auth_pass.php`, `auth_pass.Erro.php`, `auth_pass.Val.php`
- `alterarsenha.php`
- `logoff.php`
- `importacaoUsuarios/`
- `copiar_permissoes.php`

---

### Fase 3 – Módulos Principais (Próximos)

| Ordem | Módulo                  | Pasta(s) Legada(s) Principal(is)                          | Status Estimado     | Arquivos/Folders para remover após migração |
|-------|-------------------------|-----------------------------------------------------------|---------------------|---------------------------------------------|
| 3     | **Pacientes**           | `list_pacientes.php`, `infopaciente.php`, `dados_paciente.php` | Alta prioridade     | `list_pacientes.php`, `infopaciente*.php`, `formPacienteAjax.php` |
| 4     | **Agendamento**         | `agendamento/`, `agendamentoConsulta/`, `fazer_agendamento*` | Alta                | Toda pasta `agendamento/` e `agendamentoConsulta/` |
| 5     | **Prontuário**          | `prontuario/`, `prontuarioEletronico/`, `anamnese*`      | Média-Alta          | Toda pasta `prontuario/` |
| 6     | **Farmácia / Dispensação** | `farmacia/`, `dispensa_medicamentos*`, `dispensacao*` | Média               | Toda pasta `farmacia/` |
| 7     | **Exames**              | `exame/`, `exames/`, `exa_*`                              | Média               | Pastas `exame/`, `exames/` |
| 8     | **Relatórios**          | `relatorio/`, `relatorios_gerenciais/`                    | Média               | Pastas de relatórios |
| 9     | **e-SUS / Integrações** | `e-sus/`, `exportacao_esus/`, `importacao*`               | Baixa-Média         | Pastas relacionadas |
| 10    | **Módulos Específicos** | `preNatal/`, `hiperdia/`, `hanseniase/`, etc.            | Baixa               | Conforme demanda |

---

### Fase 4 – Limpeza Final (Após todos módulos principais)

**Arquivos que poderão ser removidos no final:**

- Todos os arquivos `.php` soltos na raiz (exceto `index.php`, `migrate.php`, `seed.php`)
- Pastas obsoletas: `teste/`, `testCase/`, `testeprenatal/`, `photo_booth_*`
- Pastas legadas antigas: `zf/`, `lib/`, `helper/` (se não mais usadas)
- Assets antigos: `imagens/`, `images/`, `imgsBotoes/`, etc.

---

## Critérios para Remover um Módulo Legado

1. Novo módulo está em produção há pelo menos 30 dias
2. Não há mais acessos diretos aos arquivos legados (verificar logs)
3. Todas as funcionalidades foram migradas e testadas
4. Equipe foi treinada na nova interface
5. Criar redirecionamento temporário (opcional) antes da remoção definitiva

---

## Como Usar este Roadmap

- Marque os itens como `[x]` conforme for concluindo
- Atualize a coluna "Status Estimado" periodicamente
- Crie uma issue para cada módulo grande

**Próximo passo recomendado:** Finalizar **Gestão de Usuários** e iniciar **Cadastro de Pacientes**.

---

**Quer que eu gere também:**
- Uma versão mais detalhada por módulo?
- Um template de Migration Plan para um módulo específico (ex: Pacientes)?

É só falar!