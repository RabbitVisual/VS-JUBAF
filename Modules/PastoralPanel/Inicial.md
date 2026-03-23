Essa é uma decisão estratégica de **UX (Experiência do Usuário)** e **Eclésiologia Digital** brilhante. Na prática, o Pastor é o "CEO espiritual" da igreja, e ele não deve perder tempo configurando chaves de API ou Gateways de Pagamento; ele precisa focar nas **pessoas (Ovelhas)** e na **Palavra**.

Para isso, vamos criar o **Modules\PastoralPanel**. Ele será um ambiente imersivo, com uma linguagem visual mais acolhedora e focado em "Cuidado e Alimentação" do rebanho.

---

### 🏛️ O Conceito: Painel de Pastoreio (The Shepherd's Desk)

Diferente do Admin (que é técnico e funcional), o **Painel Pastor** será focado em:

1. **Cuidado (Rebanho):** Acesso rápido aos membros, aniversariantes, doentes e árvores genealógicas.
2. **Alimento (Palavra):** Sermon Studio e Bíblia como ferramentas centrais.
3. **Ensino (EBD/Academy):** Supervisão do crescimento dos alunos.
4. **Governança (Conselho/Tesouraria):** Visão macro de aprovações e saúde financeira (transparência), sem a complexidade da entrada de notas fiscais.

---

### 🚀 Prompt para o Cursor: Criação do Modules\PastoralPanel

Copie e cole este prompt no modo **Plan** para que o Cursor construa essa estrutura de ponta a ponta:

```markdown
# PROJETO: Criação do Módulo PastoralPanel (O Gabinete do Pastor) - VertexCBAV
# OBJETIVO: Separar a gestão técnica (Admin) da gestão ministerial (Pastor), criando um painel focado em pastoreio.

Atue como Arquiteto de Software e Especialista em Gestão Eclesiástica. Quero criar o `Modules\PastoralPanel`, um ambiente exclusivo para o Pastor e sua equipe ministerial.

## 1. Arquitetura e Rotas
- Crie o módulo `Modules\PastoralPanel` com estrutura completa (app, resources, routes).
- Defina o prefixo de rota `/pastor` (ex: `pastor.dashboard`).
- Implemente o Middleware `Role:Pastor` para garantir que apenas usuários com nível pastoral acessem este painel.

## 2. Dashboard Ministerial (O Coração do Pastor)
- O Dashboard deve ser visualmente diferente do Admin: use tons mais quentes (ex: Navy/Gold ou Forest Green) e tipografia elegante.
- Widgets de Destaque:
    - **Total de Ovelhas:** Link direto para a lista de membros.
    - **Pedidos de Oração:** Notificações do módulo `Intercessor`.
    - **Próximos Sermões:** Atalhos para o `Sermon Studio`.
    - **Aniversariantes da Semana:** Lista rápida para contato pastoral.

## 3. Menu e Funcionalidades (Ponta a Ponta)
Reúna as funcionalidades de outros módulos, simplificando a interface para o Pastor:
- **Rebanho (Members):** Visualização completa de perfis e Árvores Genealógicas (sem campos técnicos de sistema).
- **Estúdio da Palavra (Sermons):** Acesso total ao Sermon Studio e Bíblia.
- **Educação:** Visão macro da `EBD` e `Worship Academy` (progresso dos alunos).
- **Ministérios:** Gestão das lideranças e escalas.
- **Transparência (Treasury):** Gráficos de entradas/saídas e balancetes (Leitura), sem as configurações de conta bancária/gateway.
- **Conselho (ChurchCouncil):** Atas e aprovações pendentes.

## 4. UI/UX "Shepherd Style"
- Use ícones Font Awesome Duotone específicos: `fa-shepherd-hook` (ou `fa-staff`), `fa-book-bible`, `fa-hands-praying`, `fa-users-rays`.
- Adicione o **Bot Elias** como um "Conselheiro Estratégico" no Dashboard, oferecendo insights sobre a saúde da igreja (ex: "Pastor, detectamos uma queda na frequência da EBD, deseja analisar?").

## 5. Migração de Poder
- Garanta que o Pastor tenha permissão de "Super Admin" no banco de dados, mas a interface do `PastoralPanel` deve ocultar:
    - Configurações de reCAPTCHA, Timezones, Gateways, Logs de Erro de Sistema e Manutenção.
- No Dashboard do Admin Técnico, adicione um botão: "Ir para Gabinete Pastoral".

Instrução Técnica: Utilize Componentes Blade compartilhados para evitar duplicidade de código, mas personalize os layouts para que o Pastor sinta que está em um ambiente dedicado ao ministério.

```

---

### 🛡️ Por que isso é uma solução de "Elite"?

1. **Foco no Ministério:** O Pastor não é um TI. Ao remover o "lixo técnico" da frente dele, você aumenta a produtividade dele no que realmente importa: a pregação e o aconselhamento.
2. **Segurança Psicológica:** O Admin técnico pode parecer intimidador. No **Gabinete Pastoral**, ele se sente "em casa", cercado por termos que ele entende (Ovelhas, Palavra, Altar, Conselho).
3. **Delegar sem Perder o Controle:** O Pastor tem o poder total (Admin no banco), mas a interface protege ele de desconfigurar algo técnico por acidente.
4. **Dashboard de Decisão:** O Elias no painel pastoral não fala de "erros de servidor", ele fala de "saúde da congregação", transformando o sistema em um braço direito do ministério.

**Deseja que eu prepare uma "Página de Boas-Vindas" especial para o Pastor, com um versículo de encorajamento que mude a cada acesso ao painel?** Conclua a rodada no Cursor e me envie o feedback!
