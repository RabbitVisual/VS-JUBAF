# VS-JUBAF: Plataforma de Gestão da Juventude Batista Feirense

## 📌 1. Visão Geral do Sistema
O **VS-JUBAF** (Vertex Solutions - Juventude Batista Feirense) é um ecossistema de software robusto desenvolvido em Laravel (HMVC com `nwidart/laravel-modules`). Ele foi arquitetado exclusivamente para atender às complexidades de uma **Associação de Jovens**, rompendo com os padrões de softwares de igrejas locais.

Sua missão é atuar como o braço digital da **Diretoria (Gabinete)**, automatizando a gestão macro da associação, enquanto conecta, capacita e provê recursos para as **Lideranças Locais** e para a **Juventude** das igrejas filiadas.

---

## 🎯 2. Propósito Estratégico
O sistema foi desenhado para resolver as seguintes dores de uma Associação:
1. **Gestão Descentralizada:** Organizar dezenas de igrejas, congregações e seus respectivos líderes em um único banco de dados, respeitando hierarquias de acesso.
2. **Motor de Eventos e Finanças:** Eliminar planilhas e conferência manual de comprovantes de PIX. O sistema gerencia lotes, recebe pagamentos, emite ingressos com QR Code e consolida tudo na Tesouraria automaticamente.
3. **Comunicação Oficial e Imediata:** Garantir que editais, avisos e campanhas cheguem diretamente ao smartphone do jovem, sem depender de repasses por grupos de WhatsApp.
4. **Hub de Crescimento (Recursos):** Prover materiais de qualidade (esboços de sermões, desafios bíblicos) para apoiar os ministérios locais que não possuem equipe própria de criação.

---

## 👥 3. Os Atores do Sistema (RBAC)
O controle de acesso é blindado pela biblioteca Spatie Permission. Existem fronteiras rígidas entre os painéis:

* **👑 Gabinete / Diretoria (`AdminPanel`):** Acesso exclusivo para o Presidente, Vice, Secretário e Tesoureiro. Visão "Macro". Podem aprovar igrejas, gerenciar o caixa central, criar eventos globais, postar no Mural Oficial e gerenciar as reuniões e atas da Associação.
* **🛡️ Liderança Local (`LiderancaPanel`):** Acesso para o líder de jovens da igreja local. Visão "Micro". Ele acessa o sistema para gerenciar a sua *Caravana* (ver quais de seus jovens se inscreveram no acampamento da JUBAF) e consumir esboços/recursos para suas reuniões locais.
* **🔥 Jovem Associado (`MemberPanel`):** O usuário final. Acessa para se inscrever em acampamentos, baixar seus ingressos, participar de Desafios Bíblicos da Associação e ler os avisos oficiais.

---

## 🧩 4. Mapa de Módulos e Funcionalidades

O sistema é composto por 13 módulos interdependentes, organizados em 4 grandes pilares:

### Pilar I: Gestão Estratégica e Institucional
* **`Diretoria`:** O coração da governança. Gerencia Reuniões do conselho/gabinete, Pautas, Votações e emissão de Atas em PDF. Substitui lógicas de "Conselho de Igreja" para uma estrutura corporativa de Associação.
* **`Igrejas`:** Cadastro e geolocalização das igrejas matrizes e congregações filiadas à JUBAF. Define de onde vêm os jovens e para onde os recursos são direcionados.
* **`Admin`:** Módulo base de configurações de sistema, gestão avançada de usuários, atribuição de papéis (Roles) e permissões.

### Pilar II: O Motor de Eventos e Tesouraria
* **`Events`:** Gestão completa de Acampamentos, Congressos e Treinamentos. Controle de vagas, viradas de lotes programadas, geração de Ingressos Premium em PDF (com QR Code) e aplicativo nativo de Scanner Mobile para Check-in expresso na portaria do evento.
* **`PaymentGateway`:** O processador de pagamentos. Integração via Webhooks (PIX/Cartão) que escuta aprovações financeiras, confirma inscrições automaticamente e evita overbooking.
* **`Treasury`:** O cofre da JUBAF. Painel executivo estilo "Banking". Controla as anuidades das igrejas, recebimentos automáticos do módulo de Eventos e despesas da Diretoria.

### Pilar III: Comunicação e Engajamento
* **`HomePage`:** O portal público e institucional. A vitrine da Associação com agenda de eventos, últimas notícias e portal de entrada para novos jovens.
* **`Comunicacao`:** O Mural Oficial da JUBAF. Onde a diretoria publica editais, avisos e campanhas gerais para todos os associados logados.
* **`Notifications`:** O sistema nervoso central. Quando a inscrição de um jovem é confirmada ou um novo edital é postado, este módulo dispara alertas in-app (Sininho na NavBar) e e-mails de forma assíncrona.

### Pilar IV: Hub de Recursos e Dashboards
* **`Sermons`:** A biblioteca global de conteúdos. A JUBAF e colaboradores cadastram esboços de pregações e estudos. Os líderes locais acessam através de uma interface de leitura limpa e imersiva (Modo Leitura).
* **`Bible`:** Desafios Bíblicos comunitários da Associação. Gamificação do progresso de leitura com barras de progresso modernas.
* **`MemberPanel` & `LiderancaPanel`:** Os dashboards de boas-vindas customizados. O jovem vê "Meus Eventos e Desafios", enquanto o líder vê "Estatísticas da Minha Caravana e Últimos Sermões".

---

## 🛠️ 5. Diretrizes de Arquitetura e UI/UX (Para Agentes de IA)

Sempre que um agente de Inteligência Artificial for atuar neste repositório, deve seguir rigorosamente as seguintes regras:

1. **Mentalidade de Associação:** NUNCA crie regras de negócios voltadas para "Igreja Local" (ex: dízimos individuais, carta de transferência, punição de membros). Pense sempre em macro-gestão (JUBAF para Lideranças).
2. **Isolamento de Painéis:** As rotas do grupo `/admin` são protegidas por middlewares estritos do Gabinete. Rotas de `/lideranca` e `/membro` têm suas próprias barreiras. Não misture controllers.
3. **Design System (Flowbite/Tailwind):** Todas as views devem refletir um aplicativo de alto nível (estilo Nubank/Netflix). Utilize cards limpos, sombras sutis (`shadow-sm`, `shadow-md`), badges arredondados (`rounded-full`), layouts responsivos com grids e modais imersivos de tela cheia para tarefas operacionais (como o Scanner de Check-in).
4. **Event-Driven:** A comunicação entre módulos deve acontecer por Eventos e Listeners, mantendo o baixo acoplamento (Ex: `Events` dispara um pagamento no `PaymentGateway`, que notifica a `Treasury` e o `Notifications`).