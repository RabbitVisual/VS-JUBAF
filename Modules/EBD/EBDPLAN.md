# PROJETO: Upgrade Completo Módulo EBD (LMS Profissional) - VertexCBAV
# OBJETIVO: Transformar a EBD em uma plataforma EAD integrada, intuitiva e doutrinariamente sólida.

Atue como Engenheiro de Software Sênior e Especialista em UX de EAD. O módulo `Modules\EBD` precisa de um upgrade "ponta a ponta" para remover a confusão atual e criar um fluxo de aprendizado real.

## 1. Reestruturação do Domínio (O Fluxo de Dados)
Refine os modelos para criar uma hierarquia lógica:
- **EBDCourse:** O currículo/revista (Ex: "Panorama do Antigo Testamento").
- **EBDClass (Turmas):** A instância real (Ex: "Classe de Adultos - 2026"). Deve obrigatoriamente ter um `ministry_id` vinculado ao Ministério de Educação Cristã/EBD.
- **EBDLesson (Lições):** Vinculadas ao Curso. Campos: Título, Descrição, Ordem, Vídeo (URL), PDF de Apoio.
- **EBDAttendance:** Registro de presença vinculado à Lição e ao Aluno.
- **EBDStudentProgress:** Tabela para marcar lições como "Concluídas".

## 2. Experiência do Aluno (Dashboard EAD)
Crie uma interface de aluno no `MemberPanel` que seja envolvente:
- **Player de Lição:** Uma view limpa com o vídeo (YouTube/Vimeo) em destaque, material para download abaixo e botão "Marcar como Concluída".
- **Barra de Progresso:** Visualização percentual de quanto do curso o aluno já concluiu.
- **Meu Arcade:** Integração direta com o `Modules\Gamification`. Ao concluir lições ou acertar Quizzes, o aluno ganha XP e Medalhas automaticamente.

## 3. Ferramentas do Professor (Painel de Gestão)
O professor precisa de ferramentas rápidas no `MemberPanel\Teacher`:
- **Chamada Digital:** Uma lista simples dos alunos da turma para marcar presença com um clique (Ícone: check-circle).
- **Gestão de Avaliações:** Criar Quizzes (Múltipla escolha) vinculados às lições para testar o conhecimento.
- **Feedback:** Espaço para o professor deixar comentários sobre o progresso do aluno.

## 4. Admin & Governança (Conselho)
- **Dashboard Admin:** Visão macro de frequência (quem está faltando?), média de notas e turmas mais engajadas.
- **Integração ChurchCouncil:** Novos currículos (EBDCourse) devem poder ser enviados para "Homologação" no Conselho antes de ficarem disponíveis.

## 5. UI/UX "Awesome" & Navegação
- **Sidebar:** Organize os links conforme o perfil (Admin, Professor, Aluno).
- **Ícones FA:** Use `fa-graduation-cap` para cursos, `fa-users-viewfinder` para turmas, `fa-book-open` para lições.
- **Padronização:** Use o sistema de abas (Tabs) e cards que implementamos no módulo `Ministries`.

## Instruções Técnicas:
1. Revise as migrações existentes e aplique a nova hierarquia sem perder os dados de membros já cadastrados.
2. Implemente Services para: `EnrollmentService` (matrícula), `AttendanceService` (presença) e `GamificationBridge` (atribuição de XP).
3. Garanta que todas as views em `resources/views/memberpanel` e `resources/views/admin` sigam o layout master do projeto com `<x-loading-overlay />`.
