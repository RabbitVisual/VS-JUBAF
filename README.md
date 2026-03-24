# VS-JUBAF: Sistema de Gestão da Juventude Batista Feirense 🚀

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

O **VS-JUBAF** (desenvolvido pela Vertex Solutions) é uma plataforma de software arquitetada exclusivamente para a **JUBAF - Juventude Batista Feirense**, braço jovem da Associação Batista Feirense (ASBAF).

## 🌍 O Contexto: O que é a JUBAF?
A Associação Batista Feirense abrange dezenas de igrejas e congregações batistas em Feira de Santana (Bahia) e região. A **JUBAF** tem a missão de integrar, capacitar e mobilizar os jovens de todas essas igrejas através de grandes eventos (como Acampamentos, Congressos e Conferências), desafios de leitura bíblica e campanhas missionárias.

**O Problema:** Softwares cristãos tradicionais são focados na "Igreja Local" (gestão de dízimos, rol de membros, EBD local). Uma Associação não pastoreia membros individuais, ela gerencia **Líderes, Igrejas, Inscrições em Massa e Arrecadação Global**.

**A Solução (VS-JUBAF):** Um sistema focado no ecossistema associativo. Ele elimina planilhas de papel, automatiza cobranças via PIX, gera ingressos com QR Code para os acampamentos e funciona como um Hub de Recursos (sermões e planos bíblicos) para apoiar os ministérios locais.

---

## 🏗️ Arquitetura do Sistema
O VS-JUBAF utiliza a arquitetura **HMVC (Hierarchical Model-View-Controller)** no Laravel, isolando contextos através da biblioteca `nwidart/laravel-modules`. O banco de dados é único, mas as lógicas de negócio são estritamente separadas.

### Os 3 Pilares de Acesso (Painéis)
O sistema possui fronteiras rígidas de acesso (RBAC via Spatie Permission):

1. 👑 **AdminPanel (Gabinete da Diretoria):** Visão macro. Presidente, Tesoureiro e Secretário usam para criar eventos, acompanhar a saúde financeira central, moderar o portal público e gerenciar igrejas filiadas.
2. 🛡️ **LiderancaPanel (Líder Local):** Visão micro. O Líder de Jovens da igreja local usa para acompanhar a sua "Caravana" (quais jovens de sua igreja pagaram o acampamento) e buscar materiais de estudo para suas reuniões locais.
3. 🔥 **MemberPanel (Jovem Associado):** Visão do usuário. O jovem entra para se inscrever em eventos, baixar seu ingresso em PDF, ler notificações da diretoria e participar dos Desafios Bíblicos.

---

## 🧩 Módulos do Sistema

O sistema é composto por **13 Módulos Ativos**, operando em total sinergia:

### 1. Gestão Administrativa
* **`Admin`:** Core do sistema. Gestão avançada de usuários, perfis (Roles) e permissões de acesso global.
* **`Diretoria`:** Governança corporativa da associação. Gestão de pautas, votações de conselho e arquivamento de atas oficiais.
* **`Igrejas`:** Cadastro geolocalizado das igrejas e congregações pertencentes à ASBAF.

### 2. O Motor Financeiro e Eventos
* **`Events`:** O motor de Acampamentos e Congressos. Controla lotes de inscrições, emite ingressos nominais em PDF com QR Code e possui uma tela mobile (Scanner) para a portaria do evento aprovar check-ins em tempo real.
* **`PaymentGateway`:** Processador de checkout. Gera PIX Copia-e-Cola e QR Code dinâmicos para eventos. Possui Webhooks integrados que dão baixa automática no sistema quando o banco confirma o pagamento.
* **`Treasury`:** A "Conta Digital" da JUBAF. Dashboard executivo moderno que centraliza o caixa da associação. Recebe os pagamentos vindos dos eventos automaticamente, separados por Igreja no metadata.

### 3. Engajamento e Comunicação
* **`HomePage`:** Vitrine pública da JUBAF. Exibe os próximos grandes eventos, lema da gestão atual e as notícias do mural oficial.
* **`Comunicacao`:** Módulo onde o Gabinete lança editais, notícias e avisos.
* **`Notifications`:** O sistema de alertas (Sininho). Notifica o celular do jovem automaticamente quando sua inscrição é aprovada, ou quando um novo aviso é postado no painel da Comunicação.

### 4. Hub de Recursos (Crescimento)
* **`Sermons`:** A biblioteca da JUBAF. Um acervo colaborativo de esboços de pregações com layout imersivo focado em leitura móvel. Um recurso valioso para líderes locais que precisam de apoio na montagem de cultos de jovens.
* **`Bible`:** Focado em Desafios Bíblicos associativos. Exibe planos de leitura (Ex: "Desafio JUBAF: Evangelhos em 30 dias") com barras de progresso modernas e engajamento comunitário.

---

## 🚀 Jornadas em Destaque

### A Jornada de Inscrição Expresa
1. O Jovem acessa o **MemberPanel** e clica em "Acampamento JUBAF 2026".
2. O módulo **Events** processa a vaga no lote atual e chama o **PaymentGateway**.
3. O jovem visualiza um Checkout nativo, faz o PIX, e o Webhook do banco avisa o sistema.
4. Imediatamente: A inscrição é confirmada, o **Treasury** registra a entrada no caixa geral, o **Notifications** dispara um alerta *"Sua Vaga está garantida!"* e a tela do jovem passa a exibir um botão **Baixar Meu Ingresso (PDF com QR Code)**.
5. No dia do evento, a liderança na portaria acessa a rota do Scanner pelo celular. Aponta para o PDF do Jovem: a tela fica Verde, toca um *Bip* e o check-in está feito!

---

## 💻 Stack Tecnológico

* **Backend:** PHP 8.2+ / Laravel 11.x
* **Arquitetura Modular:** `nwidart/laravel-modules`
* **Frontend:** Blade, Alpine.js, Tailwind CSS
* **Design System / UI:** Flowbite (Premium, foco em Mobile-First)
* **Leitura de Código de Barras/QR:** HTML5-QRCode (Integrado nativamente)
* **Controle de Permissões:** Spatie Laravel Permission
* **Geração de PDF:** DomPDF / Browsershot

---

## 🛠️ Instalação e Configuração Local

Para clonar e rodar o VS-JUBAF em seu ambiente local, certifique-se de ter o PHP 8.2 e o Composer instalados.

1. **Clone o repositório:**
   ```bash
   git clone [https://github.com/RabbitVisual/VS-JUBAF.git](https://github.com/RabbitVisual/VS-JUBAF.git)
   cd VS-JUBAF