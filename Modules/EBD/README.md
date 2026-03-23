# Módulo EBD – Visão Geral e Fluxos

O módulo `EBD` é a **plataforma EAD (Ensino a Distância)** da igreja, integrando Escola Bíblica Dominical, currículos homologados pelo conselho, player de lições, gamificação (XP e medalhas), chamada digital para professores, Arcade Bíblico e o Bot Elias (IA tutor). Ele conecta Admin, MemberPanel (aluno e professor), ChurchCouncil e Gamification em um fluxo único de aprendizado.

Este documento resume **como o módulo funciona hoje**, o que foi implementado e como usar os principais recursos na prática.

---

## 1. Domínio e Principais Entidades

### Hierarquia Currículo → Turma → Lição

- **EBDCourse**
  - Representa o **currículo/revista** (ex.: "Panorama do Antigo Testamento", "Estudo de Romanos").
  - Campos principais: `name`, `slug`, `description`, `order`, `is_active`, `homologation_status` (`draft`, `pending_approval`, `approved`), `approved_at`, `approved_by`.
  - Relações: `lessons()`, `classes()`, `approvedByUser()`.
  - Escopos: `approved()`, `draft()`.
  - **Homologação:** Currículos em rascunho podem ser enviados ao Conselho; só currículos aprovados ficam disponíveis para turmas.

- **EBDClass**
  - Representa a **turma** (ex.: "Jovens 2026", "Adultos – Domingo 9h").
  - Campos principais: `name`, `course_id`, `ministry_id` (vinculação ao Ministério de Educação Cristã), `is_active`, etc.
  - Relações: `course()`, `ministry()`, `students()`, `teachers()` (via `EBDTeacher`), `lessons()`.
  - Turmas só podem usar cursos com `homologation_status = approved`.

- **EBDLesson**
  - Representa a **lição/aula** do currículo.
  - Campos principais: `title`, `description`, `course_id`, `class_id`, `order`, `video_url` (YouTube/Vimeo), `lesson_date`, `lesson_time`, `bible_book`, `bible_reference`, `introduction`, `development`, `application`, `conclusion`, `objective`, `status`.
  - Relações: `course()`, `ebdClass()`, `media()`, `materials()` (PDFs), `attendance()`, `evaluations()`.
  - Vídeo: prioriza `video_url` (embed) ou mídia local via `media`.
  - Acessório: `getVideoEmbedUrlAttribute()` gera URL de embed para YouTube/Vimeo.

- **EBDStudent**
  - Matrícula do aluno na turma.
  - Campos: `user_id`, `class_id`, `enrollment_date`, `graduation_date`, `is_active`, `notes`.

- **EBDAttendance**
  - Registro de presença em uma lição.
  - Campos: `lesson_id`, `student_id`, `status` (`present`, `absent`, `late`, `excused`), `arrival_time`, `notes`, `registered_by`.

- **EbdStudentProgress**
  - Progresso do aluno na lição (LMS).
  - Campos: `user_id`, `lesson_id`, `status` (`started`, `completed`), `completed_at`, `notes`.
  - Usado para barra de progresso do curso e controle de conclusão (com tempo mínimo obrigatório).

- **EBDEvaluation**
  - Avaliações/Quizzes vinculados à lição.
  - Campos: `lesson_id`, `user_id`, `score`, `feedback`, `status`.

- **Gamificação (EBD)**
  - `EbdGamificationPoint`: pontos de XP por ação.
  - `EbdUserAchievement`: conquistas/medalhas do aluno.
  - `GamificationLevel`: níveis (Bronze, Prata, Ouro, Platina).
  - `EbdXpRule`: regras de XP por fonte (`lesson`, `quiz`, `arcade`).

---

## 2. Serviços Principais

### 2.1. EnrollmentService

- **Responsabilidade:** Matricular e desmatricular alunos em turmas.
- **Métodos:**
  - `enroll(User $user, EBDClass $class, array $options)` → cria ou atualiza `EBDStudent`.
  - `unenroll(EBDStudent $enrollment)` → inativa a matrícula.
  - `isEnrolled(User $user, EBDClass $class)` → verifica se o usuário está matriculado ativamente.
- **Uso:** Admin `StudentController`, fluxos de matrícula.

### 2.2. AttendanceService

- **Responsabilidade:** Registrar/atualizar presença (EBDAttendance).
- **Métodos:**
  - `record(EBDLesson $lesson, EBDStudent $student, string $status, ?User $registeredBy, array $extra)`.
  - `bulkRecord(EBDLesson $lesson, array $studentIdToStatus, ?User $registeredBy)`.
  - `bulkRecordFromRows(EBDLesson $lesson, array $rows, ?User $registeredBy)` (para formulário de chamada).
- **Uso:** Admin `AttendanceController`, MemberPanel `TeacherPanelController` (chamada digital).

### 2.3. GamificationBridge

- **Responsabilidade:** Ponto único para conceder XP e reavaliar conquistas EBD.
- **Métodos:**
  - `awardLessonComplete(User $user, EBDLesson $lesson)` → adiciona XP e chama `EbdAchievementService::evaluateForUser`.
  - `awardQuizScore(User $user, int $score, string $context)`.
- **Uso:** `ClassroomController::markAsViewed`, ArcadeController, quizzes.

### 2.4. EbdLessonAvailabilityService

- **Responsabilidade:** Regras de acesso às lições (progressão sequencial).
- **Métodos:**
  - `canAccessLesson(User $user, EBDLesson $lesson)` → verifica se pode acessar (anterior concluída).
  - `getPreviousLesson(EBDLesson $lesson)`.
  - `forClassLessons(User $user, Collection $lessons)` → estados de cada lição (can_access, is_completed, is_pending, is_current).

---

## 3. Fluxos Principais (Ponta a Ponta)

### 3.1. Criação de Currículo e Homologação

**Admin → Cursos:**
1. Criação via **Wizard em 3 passos** (Alpine.js):
   - Passo 1: Dados básicos (nome, slug, descrição, ordem).
   - Passo 2: Configuração (curso ativo, texto sobre ministério).
   - Passo 3: Revisão e criar.
2. Após criar, o currículo fica em `draft`.
3. Botão **"Enviar para homologação"** (apenas quando `draft`):
   - Seta `homologation_status = pending_approval`.
   - Cria `CouncilApproval` com `approvable_type = EBDCourse`, `approval_type = ebd_curriculum`, `status = pending`.
4. No ChurchCouncil, o conselho aprova/rejeita:
   - Ao aprovar: `homologation_status = approved`, `approved_at`, `approved_by`.
   - Ao rejeitar: volta para `draft`.
5. Turmas só podem usar cursos com `homologation_status = approved`.

### 3.2. Experiência do Aluno (LMS)

**Dashboard do Aluno (`memberpanel.ebd.student.index`):**
- Hero com avatar, nível (Bronze/Prata/Ouro/Platina), boas-vindas.
- Barra de XP (progresso para próximo nível).
- Barra de progresso do curso (quando matriculado em turma com curso).
- Cards: Turmas, Badges, Player EAD (última lição), Próximos encontros, Arcade Bíblico, Presenças recentes.

**Player de Lição (`memberpanel.ebd.student.classroom.player`):**
- Layout full-screen: header, sidebar esquerda (currículo do curso), área central (vídeo + conteúdo), sidebar direita (materiais + anotações).
- **Vídeo:** YouTube/Vimeo embed quando `video_url` preenchido; senão, vídeo local.
- **Materiais:** PDFs para download.
- **Anotações:** Auto-salvas (debounce 1s).
- **Botão "Finalizar Aula":**
  - **Tempo mínimo obrigatório:** 90 segundos desde o primeiro acesso (`progress->created_at`).
  - Enquanto não atingir: botão desabilitado, exibe "Disponível em X:XX" (countdown).
  - Ao concluir: XP via `GamificationBridge`, confetes de celebração, status "Concluída".
- **Pergunte ao Elias:** Botão flutuante que abre chat lateral; integra com `CbavBotChatService` (Bíblia + contexto da lição); respostas seguem princípios batistas e Bíblia como base.

**Progressão:** O aluno só acessa a próxima lição após concluir a anterior (por `order` no curso ou por data na turma).

### 3.3. Ferramentas do Professor

**Chamada Digital (`memberpanel.ebd.teacher.attendance.manage`):**
- Lista de alunos da turma (da lição selecionada).
- **Mobile-first:** Botões grandes (min 56px) para Presente e Ausente; secundários para Atrasado e Justificado.
- Ações rápidas: "Todos Presentes" e "Todos Ausentes".
- Campos: horário de chegada e observações.
- Submit via `AttendanceService::bulkRecordFromRows`.

**Avaliações:**
- Listagem e correção em `TeacherPanelController::evaluations`, `gradeEvaluation`, `storeGrade`.
- Feedback do professor no campo `feedback` de `ebd_evaluations`.

### 3.4. Admin EBD

**Dashboard (`admin.ebd.dashboard`):**
- Métricas: classes, alunos, assiduidade, avaliações, XP total, lições concluídas, professores.
- **"Quem está faltando?"** – alunos com 2+ faltas nos últimos 30 dias.
- **"Média de notas por turma"** – agregado de avaliações por turma.
- Gráficos de tendência de presença.

**CRUD:**
- Cursos (`admin.ebd.courses`), Turmas (`admin.ebd.classes`), Lições (`admin.ebd.lessons`), Professores, Alunos, Avaliações, Presenças.
- Gamificação: Níveis XP, Medalhas, Jogos do Arcade.

---

## 4. Bot Elias (IA Tutor)

- **Onde:** No Player de Lição do aluno, botão flutuante "Pergunte ao Elias".
- **API:** `POST /painel/cbav-bot/chat` com `message` e opcional `lesson_id`.
- **Backend:** `CbavBotChatService::respond()` com contexto `lesson_title`, `lesson_description`.
- **Comportamento:** Prioriza base local (Bíblia, insights); com IA habilitada no Admin, pode delegar a provedor externo. Respostas orientadas por princípios batistas e Bíblia como base (2 Timóteo 3:16).

---

## 5. Arcade Bíblico

- Jogos: Verse Master, Quiz Bíblico, Memória, Quem disse?, Espada Afiada, Complete o Versículo, Parábolas, Hangman, Palavras Cruzadas.
- XP e medalhas ao jogar e acertar.
- Leaderboard e gamificação visual (confetes ao concluir lição).

---

## 6. UI/UX – Navegação e Padrões

### 6.1. Sidebars

**Admin (`Modules/Admin`):**
- EBD: Dashboard, **Cursos** (`fa-graduation-cap`), Turmas (`fa-users-viewfinder`), Lições (`fa-book-open`), Professores, Alunos, Avaliações, Presenças, Gamificação (Níveis, Medalhas), Configurações.

**MemberPanel:**
- **EBD: Aluno:** Dashboard, Minhas Turmas, Lições/Aulas, Meu Progresso.
- **EBD: Professor:** Painel Geral, Minhas Turmas, Todas Lições, Avaliações, Chamada.
- **EBD: Arcade:** Jogos e leaderboard.

### 6.2. Padrões Visuais

- **Tabs:** Alpine.js `x-data="{ tab: '...' }"` em cards (ex.: curso show, avaliações).
- **Cards:** `bg-white dark:bg-slate-900 rounded-3xl border`.
- **Loading:** `<x-loading-overlay />` em layouts master; formulários com `onsubmit="window.dispatchEvent(new CustomEvent('loading-overlay:show', ...))"`.
- **Ícones:** Font Awesome 7.1 Pro Duotone via `<x-icon name="..." />`.

### 6.3. Chamada Mobile-First

- Botões Presente/Ausente com `min-h-[56px]` e `touch-manipulation`.
- Grid 2 colunas para ações rápidas no mobile.

---

## 7. Integrações

- **ChurchCouncil:** Homologação de currículos (`ebd_curriculum` em `CouncilApproval`); `CouncilApprovalObserver` atualiza `EBDCourse.homologation_status`.
- **Gamification (CBAV Bot):** Chat do Elias (`CbavBotChatService`), análise e versículo do dia.
- **Bible:** Uso local para referências nas lições; Elias usa `BibleApiService` para buscas.
- **Ministries:** `ministry_id` em `EBDClass` vincula turmas ao Ministério de Educação Cristã.

---

## 8. Migrações e Dados

- **ebd_courses:** Currículos.
- **ebd_classes:** `course_id`, `ministry_id`.
- **ebd_lessons:** `course_id`, `order`, `video_url`.
- **ebd_student_progress:** LMS (status, completed_at, notes).
- **ebd_attendance:** Presenças.
- **Backfill:** Migração `backfill_ebd_courses_data` cria um curso por turma existente e preenche `course_id` e `order` nas lições.

---

## 9. Considerações para Produção

- **Tempo mínimo na aula:** 90 segundos antes de permitir "Finalizar Aula" (constante `ClassroomController::MIN_SECONDS_TO_COMPLETE`).
- **Validação backend:** `markAsViewed` verifica `progress->created_at` e rejeita se `elapsed < 90s`.
- **Seeds:** `EBDDatabaseSeeder` e `LocalDemoSeeder` populam dados de demonstração em ambiente local.
- **RBAC:** Acesso Admin via política de administrador; Professor/Aluno via roles e `EBDTeacher`.

---

## 10. Melhorias Implementadas (Resumo)

| Área | Melhoria |
|------|----------|
| Domínio | Nova hierarquia EBDCourse → EBDClass → EBDLesson; homologação de currículos |
| Serviços | EnrollmentService, AttendanceService, GamificationBridge |
| Admin | Wizard 3 passos para criar curso; CRUD cursos; Dashboard com frequência e médias |
| Aluno | Player com vídeo URL, barra de progresso, tempo mínimo, Elias chat, confetes |
| Professor | Chamada digital mobile-first; feedback em avaliações |
| ChurchCouncil | Fluxo de homologação para currículos EBD |
| UI/UX | Tabs, cards, ícones FA, loading-overlay, design alinhado ao Ministries |

O estado atual do módulo está **pronto para produção**: fluxo completo de currículo homologado, experiência EAD para aluno e professor, gamificação integrada e Bot Elias como tutor bíblico.
