/**
 * Vertex CBAV - Tours guiados (Driver.js)
 * Tours completos em português para o painel do membro.
 * Use data-tour="..." nos elementos das views e startVertexTour('tourId') para iniciar.
 */
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

const tourSteps = {
    dashboard: [
        { element: '#main-content', popover: { title: 'Bem-vindo ao seu painel', description: 'Esta é a sua área pessoal. Aqui você acompanha sua jornada, conquistas e atalhos para tudo que a igreja oferece.' }, side: 'right', align: 'start' },
        { element: '[data-tour="sidebar"]', popover: { title: 'Menu de navegação', description: 'Use o menu lateral para acessar Bíblia, Eventos, EBD, Intercessor, Doações, Ministérios e outras áreas. Passe o mouse nos ícones para ver o nome de cada seção.' }, side: 'right', align: 'start' },
        { element: '[data-tour="notifications"]', popover: { title: 'Notificações', description: 'Clique aqui para ver avisos e novidades da igreja. O número indica quantas notificações novas você tem.' }, side: 'bottom', align: 'end' },
        { element: '[data-tour="dashboard-hero"]', popover: { title: 'Sua boas-vindas', description: 'Aqui aparecem seu nome e seu nível atual. O nível sobe conforme você participa: completa o perfil, lê a Bíblia, participa de eventos e muito mais.' }, side: 'bottom', align: 'start' },
        { element: '[data-tour="dashboard-stats"]', popover: { title: 'Pontos e nível', description: 'Estes são seus pontos de experiência. Quanto mais você usar o painel e participar, mais pontos ganha e mais sobe de nível.' }, side: 'top', align: 'start' },
        { element: '[data-tour="dashboard-badges"]', popover: { title: 'Conquistas e medalhas', description: 'Conquistas que você já desbloqueou. Complete tarefas e participe para ganhar novas medalhas.' }, side: 'left', align: 'start' },
        { element: '[data-tour="dashboard-profile-completion"]', popover: { title: 'Completude do perfil', description: 'Quanto mais completo seu cadastro, mais a igreja pode te conhecer e mais pontos você pode ganhar. Clique em Perfil para editar.' }, side: 'left', align: 'start' },
        { element: '[data-tour="daily-reading"]', popover: { title: 'Leitura do dia', description: 'Uma recomendação de leitura bíblica por dia. Clique em "Ler agora" para abrir o capítulo direto na Bíblia digital.' }, side: 'left', align: 'start' },
        { element: '[data-tour="dashboard-quick-actions"]', popover: { title: 'Acesso rápido', description: 'Atalhos para Perfil, Bíblia, Eventos e Doações. Use para navegar com um clique sem precisar abrir o menu.' }, side: 'left', align: 'start' },
        { popover: { title: 'Pronto!', description: 'Agora você conhece o seu painel. Em qualquer página, use o botão "Tour desta página" no Elias (canto inferior) para rever o guia daquela tela.' } },
    ],
    profile: [
        { element: '[data-tour="profile-photo"]', popover: { title: 'Sua foto de perfil', description: 'Esta é a sua foto exibida no painel. Você pode alterá-la em "Editar Cadastro".' }, side: 'right', align: 'start' },
        { element: '[data-tour="profile-identity"]', popover: { title: 'Seus dados', description: 'Nome e e-mail cadastrados. Mantenha sempre atualizados para a igreja poder te contatar.' }, side: 'bottom', align: 'start' },
        { element: '[data-tour="profile-level"]', popover: { title: 'Seu nível', description: 'O nível reflete sua participação no painel. Complete o perfil e participe das atividades para subir.' }, side: 'top', align: 'start' },
        { element: '[data-tour="profile-points"]', popover: { title: 'Pontos de experiência', description: 'Você ganha pontos ao completar perfil, ler a Bíblia, participar de eventos e mais. Eles ajudam a subir de nível.' }, side: 'top', align: 'start' },
        { element: '[data-tour="profile-edit"]', popover: { title: 'Editar cadastro', description: 'Clique aqui para atualizar seus dados, foto e preferências. Perfil completo rende mais pontos e conquistas.' }, side: 'left', align: 'start' },
        { popover: { title: 'Perfil completo', description: 'Mantenha seus dados em dia para a igreja te conhecer melhor e para desbloquear conquistas.' } },
    ],
    'profile-edit': [
        { element: '[data-tour="profile-edit-nav"]', popover: { title: 'Seções do cadastro', description: 'Use este menu para ir direto a cada parte do formulário: Dados Básicos, Contato, Endereço, Jornada Espiritual, Profissional, Emergência e Segurança.' }, side: 'right', align: 'start' },
        { element: '[data-tour="profile-edit-personal"]', popover: { title: 'Dados básicos', description: 'Nome, CPF, data de nascimento e gênero. Preencha com seus dados reais para o perfil ficar completo.' }, side: 'left', align: 'start' },
        { element: '[data-tour="profile-edit-contact"]', popover: { title: 'Contato', description: 'Telefone e celular. A igreja usa esses dados para avisos e contato quando necessário.' }, side: 'left', align: 'start' },
        { element: '[data-tour="profile-edit-address"]', popover: { title: 'Endereço', description: 'Onde você mora. Ajuda a igreja a te localizar e a planejar ações na sua região.' }, side: 'left', align: 'start' },
        { element: '[data-tour="profile-edit-spiritual"]', popover: { title: 'Jornada espiritual', description: 'Data de batismo, de membro e tempo de congregação. Esses dados ajudam a igreja a te acompanhar.' }, side: 'left', align: 'start' },
        { element: '[data-tour="profile-edit-security"]', popover: { title: 'Segurança e fotos', description: 'Aqui você pode trocar sua senha e gerenciar suas fotos de perfil. Mantenha sua conta segura.' }, side: 'left', align: 'start' },
        { element: '[data-tour="profile-edit-submit"]', popover: { title: 'Salvar alterações', description: 'Depois de preencher ou alterar qualquer campo, clique aqui para salvar. Perfil completo pode render mais pontos!' }, side: 'top', align: 'center' },
        { popover: { title: 'Cadastro em dia', description: 'Quanto mais completo seu perfil, mais a igreja te conhece e mais você pode aproveitar o painel.' } },
    ],
    notifications: [
        { element: '[data-tour="notifications-list"]', popover: { title: 'Central de notificações', description: 'Todas as suas notificações e avisos da igreja aparecem aqui. Novos eventos, lembretes e mensagens importantes.' }, side: 'left', align: 'start' },
        { element: '[data-tour="notifications-read-all"]', popover: { title: 'Marcar todas como lidas', description: 'Clique aqui para marcar todas as notificações como lidas de uma vez. O ícone do sino no topo para de mostrar o número de novas.' }, side: 'bottom', align: 'start' },
        { popover: { title: 'Fique por dentro', description: 'Confira esta página sempre que o ícone de sino no topo mostrar novidades. Assim você não perde nada.' } },
    ],
    'bible-read': [
        { element: '[data-tour="bible-version"]', popover: { title: 'Versão da Bíblia', description: 'Escolha a tradução que deseja ler: NVI, ARA e outras. Cada uma tem uma linguagem um pouco diferente.' }, side: 'bottom', align: 'start' },
        { element: '[data-tour="bible-book"]', popover: { title: 'Livros e capítulos', description: 'Selecione um livro da Bíblia e depois o número do capítulo. Os versículos aparecem na tela ao lado ou abaixo.' }, side: 'right', align: 'start' },
        { element: '[data-tour="bible-search-link"]', popover: { title: 'Buscar na Bíblia', description: 'Clique aqui para pesquisar por palavra ou tema em todas as escrituras. Muito útil para estudos.' }, side: 'bottom', align: 'start' },
        { popover: { title: 'Bíblia digital', description: 'Aproveite a leitura. Você pode marcar versículos como favoritos para reler depois na seção Favoritos.' } },
    ],
    'bible-book': [
        { element: '#main-content', popover: { title: 'Escolha o capítulo', description: 'Aqui estão todos os capítulos deste livro. Clique em um número para abrir e ler os versículos.' }, side: 'right', align: 'start' },
        { popover: { title: 'Leitura contínua', description: 'Depois de abrir um capítulo, você pode favoritar versículos e usar o link "Buscar" para encontrar outros trechos.' } },
    ],
    'bible-chapter': [
        { element: '[data-tour="bible-chapter-nav"]', popover: { title: 'Navegação', description: 'Use "Voltar" para voltar à lista de capítulos. Aqui você também pode trocar a versão da Bíblia (NVI, ARA, etc.).' }, side: 'bottom', align: 'start' },
        { element: '[data-tour="bible-verse"]', popover: { title: 'Versículos', description: 'Cada bloco é um versículo. Passe o mouse sobre um versículo para ver o ícone de coração: clique para adicionar aos favoritos e reler depois.' }, side: 'left', align: 'start' },
        { popover: { title: 'Favoritos e compartilhar', description: 'Versículos favoritados aparecem em Bíblia > Favoritos. Você também pode compartilhar um versículo pelo ícone ao passar o mouse.' } },
    ],
    'bible-search': [
        { element: '[data-tour="bible-search-input"]', popover: { title: 'Campo de busca', description: 'Digite uma palavra ou frase (ex.: "amor", "fé"). O sistema procura em toda a Bíblia e mostra os versículos que contêm o termo.' }, side: 'bottom', align: 'start' },
        { element: '[data-tour="bible-search-results"]', popover: { title: 'Resultados', description: 'Cada resultado mostra o livro, capítulo e versículo. Clique para abrir o capítulo completo na Bíblia.' }, side: 'left', align: 'start' },
        { popover: { title: 'Busca concluída', description: 'Use a busca para estudos e para encontrar passagens que falem de um tema.' } },
    ],
    'events-list': [
        { element: '#main-content', popover: { title: 'Eventos da igreja', description: 'Aqui você vê a programação: cultos, encontros, congressos e outras atividades. Participe e fortaleça a comunhão.' }, side: 'right', align: 'start' },
        { element: '[data-tour="events-list"]', popover: { title: 'Lista de eventos', description: 'Cada card mostra data, horário e resumo do evento. Clique em um evento para ver detalhes e se inscrever.' }, side: 'left', align: 'start' },
        { element: '[data-tour="events-my-registrations-link"]', popover: { title: 'Minhas inscrições', description: 'Depois de se inscrever, você pode acompanhar suas inscrições aqui. Veja status e dados do evento.' }, side: 'bottom', align: 'start' },
        { popover: { title: 'Participe', description: 'Escolha um evento, leia as informações e use o botão de inscrição. Alguns eventos podem pedir pagamento ou confirmação.' } },
    ],
    'events-show': [
        { element: '[data-tour="events-detail"]', popover: { title: 'Detalhes do evento', description: 'Aqui aparecem data, horário, local e descrição do evento. Leia com atenção antes de se inscrever.' }, side: 'left', align: 'start' },
        { element: '[data-tour="events-register"]', popover: { title: 'Inscrever-se', description: 'Clique neste botão para fazer sua inscrição. Se houver pagamento, você será direcionado para preencher e concluir de forma segura.' }, side: 'top', align: 'center' },
        { popover: { title: 'Inscrição confirmada', description: 'Depois de se inscrever, você pode ver o status em "Minhas inscrições" no menu de eventos.' } },
    ],
    'events-my-registrations': [
        { element: '#main-content', popover: { title: 'Minhas inscrições', description: 'Aqui estão todos os eventos em que você se inscreveu. Veja data, status (pendente, confirmado) e detalhes.' }, side: 'right', align: 'start' },
        { popover: { title: 'Acompanhe', description: 'Se a inscrição estiver pendente de pagamento, use o link indicado para concluir. Quando confirmada, você receberá as informações do evento.' } },
    ],
    'ebd-dashboard': [
        { element: '#main-content', popover: { title: 'EBD — Escola Bíblica', description: 'Aqui você acompanha lições, turmas e seu progresso na Palavra. É o espaço para crescer no conhecimento bíblico.' }, side: 'right', align: 'start' },
        { element: '[data-tour="ebd-hero"]', popover: { title: 'Seu progresso', description: 'Seu nível e seus pontos de XP (experiência). Quanto mais lições e atividades você fizer, mais sobe. Os badges mostram conquistas.' }, side: 'bottom', align: 'start' },
        { element: '[data-tour="ebd-continue"]', popover: { title: 'Continuar estudos', description: 'Se você já começou uma lição, ela aparece aqui. Clique em "Continuar Jornada" para retomar de onde parou.' }, side: 'left', align: 'start' },
        { element: '[data-tour="ebd-dashboard"]', popover: { title: 'Turmas e Arcade', description: 'Aqui você acessa suas turmas, lições e os jogos bíblicos (Arcade). Estude e divirta-se ao mesmo tempo.' }, side: 'left', align: 'start' },
        { element: '[data-tour="ebd-arcade-link"]', popover: { title: 'Arcade', description: 'Jogos para fixar a Palavra: perguntas, memória, versículos e mais. Participe e suba no ranking.' }, side: 'left', align: 'start' },
        { popover: { title: 'Cresça na Palavra', description: 'A EBD é um espaço para estudo e crescimento. Aproveite as lições e o Arcade!' } },
    ],
    'ebd-arcade': [
        { element: '#main-content', popover: { title: 'Arcade — Jogos bíblicos', description: 'Aqui você encontra jogos para praticar e memorizar a Bíblia: perguntas, memória, versículos e mais.' }, side: 'right', align: 'start' },
        { element: '[data-tour="ebd-arcade-list"]', popover: { title: 'Lista de jogos', description: 'Cada jogo tem um objetivo diferente. Clique em um para jogar. Sua pontuação pode aparecer no ranking.' }, side: 'left', align: 'start' },
        { element: '[data-tour="ebd-arcade-leaderboard"]', popover: { title: 'Ranking', description: 'Veja quem está no topo. Participe dos jogos para subir na tabela e ganhar XP.' }, side: 'left', align: 'start' },
        { popover: { title: 'Divirta-se e aprenda', description: 'Jogue quantas vezes quiser. Quanto mais você pratica, mais a Palavra fica na memória.' } },
    ],
    'ebd-student': [
        { element: '#main-content', popover: { title: 'Área do aluno', description: 'Aqui você vê suas turmas, lições e seu progresso na Escola Bíblica. Acompanhe o que já estudou e o que falta.' }, side: 'right', align: 'start' },
        { element: '[data-tour="ebd-student-classes"]', popover: { title: 'Minhas turmas', description: 'As turmas em que você está inscrito. Entre em uma turma para ver as lições disponíveis.' }, side: 'left', align: 'start' },
        { element: '[data-tour="ebd-student-lessons"]', popover: { title: 'Lições', description: 'Lista de lições. Clique em uma para assistir ou ler. Marque como concluída para acompanhar seu progresso.' }, side: 'left', align: 'start' },
        { popover: { title: 'Cresça no conhecimento', description: 'Assista às lições com calma e use o Arcade para fixar o conteúdo.' } },
    ],
    'ebd-teacher': [
        { element: '#main-content', popover: { title: 'Área do professor', description: 'Aqui você gerencia suas turmas, lições, presenças e avaliações como professor da EBD.' }, side: 'right', align: 'start' },
        { element: '[data-tour="ebd-teacher-classes"]', popover: { title: 'Minhas turmas', description: 'Turmas que você leciona. Clique em uma para ver os alunos e as lições.' }, side: 'left', align: 'start' },
        { element: '[data-tour="ebd-teacher-lessons"]', popover: { title: 'Lições', description: 'Acesse o conteúdo das lições para preparar a aula e aplicar em sala.' }, side: 'left', align: 'start' },
        { popover: { title: 'Ministério de ensino', description: 'Use as ferramentas de presença e avaliações para acompanhar sua turma.' } },
    ],
    'intercessor-room': [
        { element: '#main-content', popover: { title: 'Mural de intercessão', description: 'Aqui a igreja ora junta. Você vê os pedidos de oração e pode se comprometer a orar por alguém. Também pode enviar seu próprio pedido.' }, side: 'right', align: 'start' },
        { element: '[data-tour="intercessor-filters"]', popover: { title: 'Filtros', description: 'Recentes: todos os pedidos. Alta prioridade: pedidos urgentes. Novos: pedidos que ainda não têm muitos intercessores.' }, side: 'right', align: 'start' },
        { element: '[data-tour="intercessor-new-request"]', popover: { title: 'Novo pedido', description: 'Clique aqui para enviar seu pedido de oração. Escolha uma categoria e descreva de forma que a rede possa orar por você.' }, side: 'bottom', align: 'start' },
        { element: '[data-tour="intercessor-requests"]', popover: { title: 'Lista de pedidos', description: 'Cada card é um pedido. Clique em "Sala de guerra" para entrar e se comprometer a orar por aquela pessoa.' }, side: 'left', align: 'start' },
        { popover: { title: 'A oração move montanhas', description: 'Cada pedido é levado à rede. Sua participação faz a diferença. Ore e seja orado.' } },
    ],
    'intercessor-requests-list': [
        { element: '#main-content', popover: { title: 'Meus pedidos de oração', description: 'Aqui aparecem os pedidos que você enviou. Você pode ver status, quantas pessoas se comprometeram a orar e adicionar testemunhos.' }, side: 'right', align: 'start' },
        { element: '[data-tour="intercessor-request-create-link"]', popover: { title: 'Novo pedido', description: 'Clique aqui para enviar um novo pedido de oração. Preencha título, categoria e descrição.' }, side: 'bottom', align: 'start' },
        { popover: { title: 'Seus pedidos', description: 'Quando Deus responder, você pode registrar um testemunho no pedido para edificar a igreja.' } },
    ],
    'intercessor-request-create': [
        { element: '[data-tour="intercessor-form"]', popover: { title: 'Enviar pedido de oração', description: 'Preencha o título e a descrição do seu pedido. Escolha a categoria (saúde, família, trabalho, etc.) e a prioridade se necessário.' }, side: 'left', align: 'start' },
        { popover: { title: 'Rede de intercessão', description: 'Depois de enviar, seu pedido aparece no Mural. Outros irmãos podem entrar na "Sala de guerra" e se comprometer a orar por você.' } },
    ],
    'intercessor-room-show': [
        { element: '[data-tour="intercessor-room-detail"]', popover: { title: 'Sala de guerra', description: 'Este é o pedido de oração. Aqui você vê os detalhes e pode se comprometer a orar por esta pessoa.' }, side: 'left', align: 'start' },
        { element: '[data-tour="intercessor-room-commit"]', popover: { title: 'Comprometer-se a orar', description: 'Clique aqui para registrar que você está orando. Quando terminar, pode marcar como concluído. Sua participação fortalece a rede.' }, side: 'top', align: 'center' },
        { popover: { title: 'Oração em conjunto', description: 'A igreja ora junta. Cada compromisso de oração é um apoio para quem pediu.' } },
    ],
    treasury: [
        { element: '#main-content', popover: { title: 'Tesouraria', description: 'Aqui você acompanha com transparência as finanças da igreja: entradas, campanhas, metas e relatórios.' }, side: 'right', align: 'start' },
        { element: '[data-tour="sidebar"]', popover: { title: 'Menu da tesouraria', description: 'Use o menu para ir a Entradas, Campanhas, Metas e Relatórios. Tudo que a igreja recebe e aplica está aqui.' }, side: 'right', align: 'start' },
        { element: '[data-tour="treasury-area"]', popover: { title: 'Conteúdo da página', description: 'Cada seção mostra informações diferentes. Use os filtros e botões para ver detalhes e períodos.' }, side: 'left', align: 'start' },
        { popover: { title: 'Transparência', description: 'A gestão financeira da igreja à disposição dos membros. Dúvidas? Fale com a liderança.' } },
    ],
    'treasury-entries': [
        { element: '[data-tour="treasury-area"]', popover: { title: 'Entradas', description: 'Registro das entradas financeiras da igreja: ofertas, dízimos e outras doações. Veja valores e datas com transparência.' }, side: 'left', align: 'start' },
        { popover: { title: 'Controle', description: 'Aqui você acompanha como os recursos entram. Quem tem permissão pode ainda registrar ou importar entradas.' } },
    ],
    'treasury-campaigns': [
        { element: '[data-tour="treasury-area"]', popover: { title: 'Campanhas', description: 'Campanhas financeiras da igreja: metas, prazos e como os valores são aplicados. Veja em qual campanha você pode contribuir.' }, side: 'left', align: 'start' },
        { popover: { title: 'Campanhas', description: 'Cada campanha tem um objetivo. Acompanhe o progresso e, se quiser ofertar, use a área de Doações.' } },
    ],
    'treasury-goals': [
        { element: '[data-tour="treasury-area"]', popover: { title: 'Metas', description: 'Metas financeiras da igreja: o que se pretende alcançar e como está o andamento. Tudo de forma transparente.' }, side: 'left', align: 'start' },
        { popover: { title: 'Metas', description: 'As metas ajudam a igreja a planejar. Você pode acompanhar aqui o progresso.' } },
    ],
    'treasury-reports': [
        { element: '[data-tour="treasury-area"]', popover: { title: 'Relatórios', description: 'Relatórios e resumos das finanças. Você pode visualizar e, se tiver permissão, exportar em Excel ou PDF.' }, side: 'left', align: 'start' },
        { popover: { title: 'Transparência', description: 'Os relatórios mostram entradas, saídas e resumos por período. Use os filtros para ajustar a visualização.' } },
    ],
    donations: [
        { element: '#main-content', popover: { title: 'Minhas doações', description: 'Aqui você vê o histórico das suas contribuições: ofertas e dízimos feitos pelo painel. Tudo registrado com segurança.' }, side: 'right', align: 'start' },
        { element: '[data-tour="donations-area"]', popover: { title: 'Lista de doações', description: 'Cada linha é uma doação que você fez. Veja valor, data e status (confirmado, pendente, etc.).' }, side: 'left', align: 'start' },
        { element: '[data-tour="donations-create-link"]', popover: { title: 'Fazer nova doação', description: 'Clique aqui para ir à tela de doação e ofertar. Você escolhe o valor e a forma de pagamento (cartão, PIX, etc.).' }, side: 'bottom', align: 'start' },
        { popover: { title: 'Deus ama quem dá com alegria', description: 'Suas ofertas e dízimos sustentam a obra. Obrigado por contribuir!' } },
    ],
    'donations-create': [
        { element: '[data-tour="donations-create-form"]', popover: { title: 'Fazer uma doação', description: 'Escolha o valor (ou digite um valor personalizado) e a forma de pagamento. O processo é seguro e a igreja recebe a oferta.' }, side: 'left', align: 'start' },
        { element: '[data-tour="donations-submit"]', popover: { title: 'Confirmar', description: 'Depois de preencher, clique aqui. Você será direcionado ao pagamento (cartão, PIX ou outro meio). Após confirmar, a doação aparece no seu histórico.' }, side: 'top', align: 'center' },
        { popover: { title: 'Cada um dê conforme determinou em seu coração', description: 'Não há valor mínimo. Dê com alegria e confiança.' } },
    ],
    sermons: [
        { element: '#main-content', popover: { title: 'Sermões e mensagens', description: 'Biblioteca de mensagens pregadas na igreja. Assista ou ouça quando quiser e marque favoritos para reler depois.' }, side: 'right', align: 'start' },
        { element: '[data-tour="sermons-list"]', popover: { title: 'Biblioteca', description: 'Lista de sermões disponíveis. Clique em um para abrir e assistir ou ouvir. Use "Favoritar" para acessar rápido depois.' }, side: 'left', align: 'start' },
        { element: '[data-tour="sermons-favorites-link"]', popover: { title: 'Meus favoritos', description: 'Sermões que você marcou como favoritos. Acesse por aqui para rever mensagens que tocaram seu coração.' }, side: 'bottom', align: 'start' },
        { popover: { title: 'A fé vem pelo ouvir', description: 'Aproveite a biblioteca para se edificar na Palavra.' } },
    ],
    'sermons-favorites': [
        { element: '[data-tour="sermons-list"]', popover: { title: 'Meus favoritos', description: 'Aqui estão os sermões que você marcou como favoritos. Clique em um para assistir ou ouvir de novo.' }, side: 'left', align: 'start' },
        { popover: { title: 'Rever mensagens', description: 'Use esta página para reler e reassistir as mensagens que mais te edificaram.' } },
    ],
    'sermons-series': [
        { element: '#main-content', popover: { title: 'Séries bíblicas', description: 'Séries de mensagens agrupadas por tema. Cada série pode ter vários episódios. Clique em uma para ver a lista.' }, side: 'right', align: 'start' },
        { element: '[data-tour="sermons-list"]', popover: { title: 'Lista de séries', description: 'Escolha uma série para ver os sermões que a compõem. Ótimo para estudos temáticos.' }, side: 'left', align: 'start' },
        { popover: { title: 'Estudo em série', description: 'Assista na ordem para acompanhar o desenvolvimento do tema.' } },
    ],
    'sermons-studies': [
        { element: '#main-content', popover: { title: 'Estudos bíblicos', description: 'Estudos e comentários disponíveis para você se aprofundar na Palavra.' }, side: 'right', align: 'start' },
        { element: '[data-tour="sermons-list"]', popover: { title: 'Lista de estudos', description: 'Clique em um estudo para ler ou assistir. Complemente a leitura da Bíblia com esses materiais.' }, side: 'left', align: 'start' },
        { popover: { title: 'Aprofunde-se', description: 'Use os estudos para crescer no conhecimento bíblico.' } },
    ],
    ministries: [
        { element: '#main-content', popover: { title: 'Ministérios', description: 'Conheça os ministérios da igreja: música, juventude, casais e outras frentes. Veja onde você pode servir.' }, side: 'right', align: 'start' },
        { element: '[data-tour="ministries-list"]', popover: { title: 'Lista de ministérios', description: 'Cada card é um ministério. Leia a descrição e, se sentir no coração, inscreva-se. Você pode sair quando quiser.' }, side: 'left', align: 'start' },
        { element: '[data-tour="ministries-join"]', popover: { title: 'Inscrever-se', description: 'No ministério que desejar, use o botão de inscrever-se. A liderança do ministério pode entrar em contato.' }, side: 'top', align: 'center' },
        { popover: { title: 'Sirva com propósito', description: 'Cada um com seu dom. Encontre seu lugar no corpo de Cristo.' } },
    ],
    churchcouncil: [
        { element: '#main-content', popover: { title: 'Conselho', description: 'Área do conselho da igreja: reuniões, pautas, aprovações, documentos e projetos. Se você faz parte do conselho, use este painel.' }, side: 'right', align: 'start' },
        { element: '[data-tour="churchcouncil-menu"]', popover: { title: 'Menu do conselho', description: 'Reuniões: ver e participar de reuniões. Pautas: ver e criar pautas. Aprovações: acompanhar solicitações. Documentos e Projetos: arquivos e projetos do conselho.' }, side: 'right', align: 'start' },
        { element: '[data-tour="churchcouncil-area"]', popover: { title: 'Conteúdo', description: 'Aqui aparece o conteúdo da seção escolhida. Use os botões e links para votar, baixar documentos ou ver detalhes.' }, side: 'left', align: 'start' },
        { popover: { title: 'Governança', description: 'Tudo que o conselho precisa para reunir, decidir e documentar está aqui.' } },
    ],
    worship: [
        { element: '#main-content', popover: { title: 'Louvor e escalas', description: 'Área de quem serve no louvor: minhas escalas, ensaio e academia. Aqui você vê quando toca e acessa setlists e cursos.' }, side: 'right', align: 'start' },
        { element: '[data-tour="worship-menu"]', popover: { title: 'Navegação', description: 'Minhas escalas: quando você está escalado. Rehearsal: ensaios e setlists. Academy: cursos de música e louvor.' }, side: 'right', align: 'start' },
        { popover: { title: 'Sirva no louvor', description: 'Use esta área para se preparar e acompanhar suas escalas e ensaios.' } },
    ],
    'worship-rosters': [
        { element: '[data-tour="worship-rosters-list"]', popover: { title: 'Minhas escalas', description: 'Aqui aparecem as escalas em que você está: data, setlist e função. Confirme presença quando necessário.' }, side: 'left', align: 'start' },
        { popover: { title: 'Fique por dentro', description: 'Confira sempre suas escalas para não perder ensaios e cultos.' } },
    ],
    'worship-rehearsal': [
        { element: '#main-content', popover: { title: 'Rehearsal — Ensaio', description: 'Aqui você acessa as setlists de ensaio e culto. Veja as músicas, cifras e ordem do louvor.' }, side: 'right', align: 'start' },
        { element: '[data-tour="worship-rehearsal-list"]', popover: { title: 'Setlists', description: 'Lista de setlists disponíveis. Clique em uma para ver as músicas e se preparar para o ensaio ou culto.' }, side: 'left', align: 'start' },
        { popover: { title: 'Prepare-se', description: 'Use as setlists para ensaiar em casa e chegar pronto no dia.' } },
    ],
    'worship-academy': [
        { element: '#main-content', popover: { title: 'Academia de louvor', description: 'Cursos e aulas para você crescer em música e ministério. Acesse os cursos e acompanhe seu progresso.' }, side: 'right', align: 'start' },
        { element: '[data-tour="worship-academy-list"]', popover: { title: 'Cursos', description: 'Lista de cursos disponíveis. Entre em um curso para ver as aulas e materiais. Complete as aulas para avançar.' }, side: 'left', align: 'start' },
        { popover: { title: 'Cresça no ministério', description: 'Aproveite a academia para melhorar técnica e ministério.' } },
    ],
    projection: [
        { element: '#main-content', popover: { title: 'Projeção', description: 'Área de projeção para cultos e reuniões. Aqui você controla o que aparece na tela (letras, versículos, slides).' }, side: 'right', align: 'start' },
        { element: '[data-tour="projection-console"]', popover: { title: 'Console', description: 'No console você escolhe o setlist e controla o que é exibido na tela. Use durante o culto para projetar letras e versículos.' }, side: 'left', align: 'start' },
        { element: '[data-tour="projection-screen"]', popover: { title: 'Tela', description: 'A tela é o que a plateia vê. Abra em um navegador ou projetor separado e sincronize com o console.' }, side: 'left', align: 'start' },
        { popover: { title: 'Uso em culto', description: 'Quem opera a projeção usa o console; a tela fica voltada para a igreja.' } },
    ],
};

/**
 * Monta o popover de cada passo com side/align.
 */
function buildSteps(tourId) {
    const steps = tourSteps[tourId];
    if (!steps || !steps.length) return [];
    return steps.map((step) => {
        const hasElement = step.element != null;
        const popover = {
            title: step.popover?.title ?? '',
            description: step.popover?.description ?? '',
            side: step.side ?? 'bottom',
            align: step.align ?? 'center',
        };
        return hasElement ? { element: step.element, popover } : { popover };
    });
}

/**
 * Inicia um tour pelo id. Configuração global em pt-BR, animada e com progresso.
 * @param {string} tourId - Chave do tour (dashboard, bible-read, profile, etc.)
 */
function startVertexTour(tourId) {
    const steps = buildSteps(tourId);
    if (!steps.length) {
        console.warn('[Vertex Tour] Tour não encontrado:', tourId);
        return;
    }

    const driverObj = driver({
        animate: true,
        showProgress: true,
        progressText: '{{current}} de {{total}}',
        nextBtnText: 'Próximo',
        prevBtnText: 'Anterior',
        doneBtnText: 'Concluir',
        popoverClass: 'vertex-tour-popover',
        smoothScroll: true,
        allowClose: true,
        overlayColor: 'rgba(0,0,0,0.65)',
        overlayOpacity: 0.65,
        stagePadding: 10,
        stageRadius: 8,
        showButtons: ['next', 'previous', 'close'],
        steps,
        onDestroyed: () => {
            try {
                localStorage.setItem('vertex_tour_completed_' + tourId, new Date().toISOString());
            } catch (e) {}
        },
    });

    driverObj.drive();
}

window.startVertexTour = startVertexTour;
export { startVertexTour, tourSteps };
