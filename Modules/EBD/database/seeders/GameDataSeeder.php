<?php

namespace Modules\EBD\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EBD\App\Models\Game;
use Modules\EBD\App\Models\GameQuestion;
use Modules\EBD\App\Models\GameAnswer;

class GameDataSeeder extends Seeder
{
    public function run()
    {
        $this->seedGames();
        $this->seedQuizQuestions();
    }

    protected function seedGames()
    {
        $games = [
            [
                'slug' => 'mestre-do-conhecimento',
                'name' => 'Mestre do Conhecimento',
                'description' => 'Teste seus conhecimentos bíblicos com mais de 200 perguntas!',
                'icon' => 'brain-circuit',
            ],
            [
                'slug' => 'arca-da-memoria',
                'name' => 'Arca da Memória',
                'description' => 'Encontre pares de personagens e eventos bíblicos.',
                'icon' => 'cards',
            ],
            [
                'slug' => 'quem-disse',
                'name' => 'Quem Disse?',
                'description' => 'Adivinhe qual personagem bíblico fez a citação.',
                'icon' => 'comment-quote',
            ],
            [
                'slug' => 'linha-do-tempo',
                'name' => 'Linha do Tempo',
                'description' => 'Ordene eventos bíblicos cronologicamente.',
                'icon' => 'hourglass-start',
            ],
            [
                'slug' => 'espada-afiada',
                'name' => 'Espada Afiada',
                'description' => 'Teste seus reflexos com os livros da Bíblia.',
                'icon' => 'sword',
            ],
            [
                'slug' => 'forca-da-fe',
                'name' => 'Forca da Fé',
                'description' => 'Adivinhe palavras bíblicas letra por letra.',
                'icon' => 'keyboard',
            ],
            [
                'slug' => 'versemaster',
                'name' => 'VerseMaster',
                'description' => 'Monte versículos arrastando palavras na ordem correta.',
                'icon' => 'scroll',
            ],
            [
                'slug' => 'caca-palavras',
                'name' => 'Caça-Palavras Bíblico',
                'description' => 'Encontre palavras bíblicas escondidas no grid.',
                'icon' => 'magnifying-glass',
            ],
            [
                'slug' => 'heroi-da-fe',
                'name' => 'Herói da Fé',
                'description' => 'Descubra personagens bíblicos através de dicas.',
                'icon' => 'user-crown',
            ],
            [
                'slug' => 'complete-o-versiculo',
                'name' => 'Complete o Versículo',
                'description' => 'Preencha as lacunas em versículos famosos.',
                'icon' => 'pen-to-square',
            ],
            [
                'slug' => 'desafio-dos-livros',
                'name' => 'Desafio dos Livros',
                'description' => 'Categorize e ordene os 66 livros da Bíblia.',
                'icon' => 'book-bible',
            ],
            [
                'slug' => 'trio-biblico',
                'name' => 'Trio Bíblico',
                'description' => 'Encontre grupos de 3 itens relacionados.',
                'icon' => 'clone',
            ],
            [
                'slug' => 'palavras-cruzadas',
                'name' => 'Palavras Cruzadas',
                'description' => 'Resolva palavras cruzadas com termos bíblicos.',
                'icon' => 'grid-2',
            ],
            [
                'slug' => 'parabolas',
                'name' => 'Parábolas em Ação',
                'description' => 'Conecte parábolas de Jesus aos seus ensinos.',
                'icon' => 'book-open',
            ],
            [
                'slug' => 'navegador-biblico',
                'name' => 'Navegador Bíblico',
                'description' => 'Encontre versículos rapidamente contra o relógio.',
                'icon' => 'compass',
            ],
        ];

        foreach ($games as $game) {
            Game::firstOrCreate(
                ['slug' => $game['slug']],
                array_merge($game, ['is_active' => true])
            );
        }
    }

    protected function seedQuizQuestions()
    {
        $quiz = Game::where('slug', 'mestre-do-conhecimento')->first();
        if (!$quiz) return;

        $questions = [
            // Antigo Testamento - Personagens
            ['q' => 'Quem foi engolido por um grande peixe?', 'category' => 'personagens', 'a' => [['text' => 'Jonas', 'correct' => true], ['text' => 'Pedro', 'correct' => false], ['text' => 'Paulo', 'correct' => false], ['text' => 'Moisés', 'correct' => false]]],
            ['q' => 'Quem abriu o Mar Vermelho?', 'category' => 'personagens', 'a' => [['text' => 'Moisés', 'correct' => true], ['text' => 'Josué', 'correct' => false], ['text' => 'Arão', 'correct' => false], ['text' => 'Elias', 'correct' => false]]],
            ['q' => 'Quem matou Golias?', 'category' => 'personagens', 'a' => [['text' => 'Davi', 'correct' => true], ['text' => 'Saul', 'correct' => false], ['text' => 'Samuel', 'correct' => false], ['text' => 'Salomão', 'correct' => false]]],
            ['q' => 'Quem construiu a Arca?', 'category' => 'personagens', 'a' => [['text' => 'Noé', 'correct' => true], ['text' => 'Abraão', 'correct' => false], ['text' => 'Adão', 'correct' => false], ['text' => 'José', 'correct' => false]]],
            ['q' => 'Quem foi o primeiro homem?', 'category' => 'personagens', 'a' => [['text' => 'Adão', 'correct' => true], ['text' => 'Caim', 'correct' => false], ['text' => 'Abel', 'correct' => false], ['text' => 'Sete', 'correct' => false]]],
            ['q' => 'Quem foi lançado na cova dos leões?', 'category' => 'personagens', 'a' => [['text' => 'Daniel', 'correct' => true], ['text' => 'Davi', 'correct' => false], ['text' => 'José', 'correct' => false], ['text' => 'Sansão', 'correct' => false]]],
            ['q' => 'Quem era o homem mais forte da Bíblia?', 'category' => 'personagens', 'a' => [['text' => 'Sansão', 'correct' => true], ['text' => 'Golias', 'correct' => false], ['text' => 'Davi', 'correct' => false], ['text' => 'Moisés', 'correct' => false]]],
            ['q' => 'Quem era o pai de Salomão?', 'category' => 'personagens', 'a' => [['text' => 'Davi', 'correct' => true], ['text' => 'Samuel', 'correct' => false], ['text' => 'Saul', 'correct' => false], ['text' => 'Absalão', 'correct' => false]]],
            ['q' => 'Quem foi vendido pelos irmãos como escravo?', 'category' => 'personagens', 'a' => [['text' => 'José', 'correct' => true], ['text' => 'Benjamim', 'correct' => false], ['text' => 'Judá', 'correct' => false], ['text' => 'Rúben', 'correct' => false]]],
            ['q' => 'Quem foi o primeiro rei de Israel?', 'category' => 'personagens', 'a' => [['text' => 'Saul', 'correct' => true], ['text' => 'Davi', 'correct' => false], ['text' => 'Salomão', 'correct' => false], ['text' => 'Samuel', 'correct' => false]]],
            ['q' => 'Quem foi o profeta que subiu ao céu em um carro de fogo?', 'category' => 'personagens', 'a' => [['text' => 'Elias', 'correct' => true], ['text' => 'Eliseu', 'correct' => false], ['text' => 'Enoque', 'correct' => false], ['text' => 'Moisés', 'correct' => false]]],
            ['q' => 'Quem foi a esposa de Abraão?', 'category' => 'personagens', 'a' => [['text' => 'Sara', 'correct' => true], ['text' => 'Rebeca', 'correct' => false], ['text' => 'Raquel', 'correct' => false], ['text' => 'Lia', 'correct' => false]]],
            ['q' => 'Quem foi o rei mais sábio?', 'category' => 'personagens', 'a' => [['text' => 'Salomão', 'correct' => true], ['text' => 'Davi', 'correct' => false], ['text' => 'Samuel', 'correct' => false], ['text' => 'Josias', 'correct' => false]]],
            ['q' => 'Quem lutou com um anjo durante toda a noite?', 'category' => 'personagens', 'a' => [['text' => 'Jacó', 'correct' => true], ['text' => 'Abraão', 'correct' => false], ['text' => 'Isaque', 'correct' => false], ['text' => 'José', 'correct' => false]]],
            ['q' => 'Quem foi a rainha que salvou seu povo do extermínio?', 'category' => 'personagens', 'a' => [['text' => 'Ester', 'correct' => true], ['text' => 'Rute', 'correct' => false], ['text' => 'Débora', 'correct' => false], ['text' => 'Miriã', 'correct' => false]]],

            // Novo Testamento - Personagens
            ['q' => 'Quem traiu Jesus?', 'category' => 'personagens', 'a' => [['text' => 'Judas Iscariotes', 'correct' => true], ['text' => 'Pedro', 'correct' => false], ['text' => 'Tomé', 'correct' => false], ['text' => 'Pilatos', 'correct' => false]]],
            ['q' => 'Quem negou Jesus três vezes?', 'category' => 'personagens', 'a' => [['text' => 'Pedro', 'correct' => true], ['text' => 'Judas', 'correct' => false], ['text' => 'Tomé', 'correct' => false], ['text' => 'João', 'correct' => false]]],
            ['q' => 'Quem era conhecido como o discípulo amado?', 'category' => 'personagens', 'a' => [['text' => 'João', 'correct' => true], ['text' => 'Pedro', 'correct' => false], ['text' => 'Tiago', 'correct' => false], ['text' => 'André', 'correct' => false]]],
            ['q' => 'Quem batizou Jesus?', 'category' => 'personagens', 'a' => [['text' => 'João Batista', 'correct' => true], ['text' => 'Pedro', 'correct' => false], ['text' => 'Paulo', 'correct' => false], ['text' => 'Tiago', 'correct' => false]]],
            ['q' => 'Quem era o apóstolo dos gentios?', 'category' => 'personagens', 'a' => [['text' => 'Paulo', 'correct' => true], ['text' => 'Pedro', 'correct' => false], ['text' => 'João', 'correct' => false], ['text' => 'Tiago', 'correct' => false]]],
            ['q' => 'Quem era a mãe de Jesus?', 'category' => 'personagens', 'a' => [['text' => 'Maria', 'correct' => true], ['text' => 'Marta', 'correct' => false], ['text' => 'Maria Madalena', 'correct' => false], ['text' => 'Isabel', 'correct' => false]]],
            ['q' => 'Quem era o pai terreno de Jesus?', 'category' => 'personagens', 'a' => [['text' => 'José', 'correct' => true], ['text' => 'Zacarias', 'correct' => false], ['text' => 'Zebedeu', 'correct' => false], ['text' => 'Simão', 'correct' => false]]],
            ['q' => 'Quem era o irmão de Pedro?', 'category' => 'personagens', 'a' => [['text' => 'André', 'correct' => true], ['text' => 'Tiago', 'correct' => false], ['text' => 'João', 'correct' => false], ['text' => 'Filipe', 'correct' => false]]],
            ['q' => 'Quem era o publicano que se converteu e subiu em uma árvore?', 'category' => 'personagens', 'a' => [['text' => 'Zaqueu', 'correct' => true], ['text' => 'Mateus', 'correct' => false], ['text' => 'Levi', 'correct' => false], ['text' => 'Nicodemos', 'correct' => false]]],
            ['q' => 'Quem foi o primeiro mártir cristão?', 'category' => 'personagens', 'a' => [['text' => 'Estêvão', 'correct' => true], ['text' => 'Tiago', 'correct' => false], ['text' => 'Pedro', 'correct' => false], ['text' => 'Paulo', 'correct' => false]]],

            // Livros da Bíblia
            ['q' => 'Qual é o último livro da Bíblia?', 'category' => 'livros', 'a' => [['text' => 'Apocalipse', 'correct' => true], ['text' => 'Gênesis', 'correct' => false], ['text' => 'Mateus', 'correct' => false], ['text' => 'Malaquias', 'correct' => false]]],
            ['q' => 'Qual é o primeiro livro da Bíblia?', 'category' => 'livros', 'a' => [['text' => 'Gênesis', 'correct' => true], ['text' => 'Êxodo', 'correct' => false], ['text' => 'Mateus', 'correct' => false], ['text' => 'Apocalipse', 'correct' => false]]],
            ['q' => 'Quantos livros tem a Bíblia?', 'category' => 'livros', 'a' => [['text' => '66', 'correct' => true], ['text' => '73', 'correct' => false], ['text' => '39', 'correct' => false], ['text' => '27', 'correct' => false]]],
            ['q' => 'Quantos livros tem o Antigo Testamento?', 'category' => 'livros', 'a' => [['text' => '39', 'correct' => true], ['text' => '27', 'correct' => false], ['text' => '46', 'correct' => false], ['text' => '66', 'correct' => false]]],
            ['q' => 'Quantos livros tem o Novo Testamento?', 'category' => 'livros', 'a' => [['text' => '27', 'correct' => true], ['text' => '39', 'correct' => false], ['text' => '21', 'correct' => false], ['text' => '22', 'correct' => false]]],
            ['q' => 'Qual livro contém as canções de Davi?', 'category' => 'livros', 'a' => [['text' => 'Salmos', 'correct' => true], ['text' => 'Provérbios', 'correct' => false], ['text' => 'Cânticos', 'correct' => false], ['text' => 'Eclesiastes', 'correct' => false]]],
            ['q' => 'Qual livro conta a história de Rute?', 'category' => 'livros', 'a' => [['text' => 'Rute', 'correct' => true], ['text' => 'Ester', 'correct' => false], ['text' => 'Juízes', 'correct' => false], ['text' => 'Samuel', 'correct' => false]]],
            ['q' => 'Qual livro é conhecido como o livro da sabedoria?', 'category' => 'livros', 'a' => [['text' => 'Provérbios', 'correct' => true], ['text' => 'Eclesiastes', 'correct' => false], ['text' => 'Salmos', 'correct' => false], ['text' => 'Jó', 'correct' => false]]],
            ['q' => 'Qual evangelho foi escrito por um médico?', 'category' => 'livros', 'a' => [['text' => 'Lucas', 'correct' => true], ['text' => 'Mateus', 'correct' => false], ['text' => 'Marcos', 'correct' => false], ['text' => 'João', 'correct' => false]]],
            ['q' => 'Qual livro conta a história da igreja primitiva?', 'category' => 'livros', 'a' => [['text' => 'Atos', 'correct' => true], ['text' => 'Romanos', 'correct' => false], ['text' => 'Coríntios', 'correct' => false], ['text' => 'Hebreus', 'correct' => false]]],

            // Lugares
            ['q' => 'Onde Jesus nasceu?', 'category' => 'lugares', 'a' => [['text' => 'Belém', 'correct' => true], ['text' => 'Nazaré', 'correct' => false], ['text' => 'Jerusalém', 'correct' => false], ['text' => 'Egito', 'correct' => false]]],
            ['q' => 'Onde Jesus cresceu?', 'category' => 'lugares', 'a' => [['text' => 'Nazaré', 'correct' => true], ['text' => 'Belém', 'correct' => false], ['text' => 'Jerusalém', 'correct' => false], ['text' => 'Cafarnaum', 'correct' => false]]],
            ['q' => 'Onde Jesus foi crucificado?', 'category' => 'lugares', 'a' => [['text' => 'Gólgota', 'correct' => true], ['text' => 'Monte das Oliveiras', 'correct' => false], ['text' => 'Monte Sinai', 'correct' => false], ['text' => 'Getsêmani', 'correct' => false]]],
            ['q' => 'Onde Moisés recebeu os Dez Mandamentos?', 'category' => 'lugares', 'a' => [['text' => 'Monte Sinai', 'correct' => true], ['text' => 'Monte Carmelo', 'correct' => false], ['text' => 'Monte das Oliveiras', 'correct' => false], ['text' => 'Monte Hermom', 'correct' => false]]],
            ['q' => 'Qual cidade foi destruída junto com Sodoma?', 'category' => 'lugares', 'a' => [['text' => 'Gomorra', 'correct' => true], ['text' => 'Jericó', 'correct' => false], ['text' => 'Babilônia', 'correct' => false], ['text' => 'Nínive', 'correct' => false]]],
            ['q' => 'Em qual rio Jesus foi batizado?', 'category' => 'lugares', 'a' => [['text' => 'Rio Jordão', 'correct' => true], ['text' => 'Rio Nilo', 'correct' => false], ['text' => 'Rio Eufrates', 'correct' => false], ['text' => 'Mar Morto', 'correct' => false]]],
            ['q' => 'Onde ficava o Jardim do Éden?', 'category' => 'lugares', 'a' => [['text' => 'Mesopotamia', 'correct' => true], ['text' => 'Israel', 'correct' => false], ['text' => 'Egito', 'correct' => false], ['text' => 'Não é especificado', 'correct' => false]]],
            ['q' => 'Qual cidade os israelitas cercaram por 7 dias?', 'category' => 'lugares', 'a' => [['text' => 'Jericó', 'correct' => true], ['text' => 'Jerusalém', 'correct' => false], ['text' => 'Ai', 'correct' => false], ['text' => 'Hazor', 'correct' => false]]],
            ['q' => 'Onde Paulo nasceu?', 'category' => 'lugares', 'a' => [['text' => 'Tarso', 'correct' => true], ['text' => 'Jerusalém', 'correct' => false], ['text' => 'Roma', 'correct' => false], ['text' => 'Antioquia', 'correct' => false]]],
            ['q' => 'Onde ficava o templo de Salomão?', 'category' => 'lugares', 'a' => [['text' => 'Jerusalém', 'correct' => true], ['text' => 'Samaria', 'correct' => false], ['text' => 'Belém', 'correct' => false], ['text' => 'Hebrom', 'correct' => false]]],

            // Números
            ['q' => 'Quantos mandamentos Deus deu a Moisés?', 'category' => 'numeros', 'a' => [['text' => '10', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '12', 'correct' => false], ['text' => '5', 'correct' => false]]],
            ['q' => 'Quantos apóstolos Jesus escolheu?', 'category' => 'numeros', 'a' => [['text' => '12', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '70', 'correct' => false], ['text' => '10', 'correct' => false]]],
            ['q' => 'Quantos dias durou o dilúvio de Noé?', 'category' => 'numeros', 'a' => [['text' => '40', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '120', 'correct' => false], ['text' => '30', 'correct' => false]]],
            ['q' => 'Quantos anos os israelitas ficaram no deserto?', 'category' => 'numeros', 'a' => [['text' => '40', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '12', 'correct' => false], ['text' => '70', 'correct' => false]]],
            ['q' => 'Quantas tribos de Israel existiam?', 'category' => 'numeros', 'a' => [['text' => '12', 'correct' => true], ['text' => '10', 'correct' => false], ['text' => '7', 'correct' => false], ['text' => '14', 'correct' => false]]],
            ['q' => 'Quantos dias Jesus ficou no deserto sendo tentado?', 'category' => 'numeros', 'a' => [['text' => '40', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '30', 'correct' => false], ['text' => '12', 'correct' => false]]],
            ['q' => 'Quantos dias Jonas ficou na barriga do peixe?', 'category' => 'numeros', 'a' => [['text' => '3', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '40', 'correct' => false], ['text' => '1', 'correct' => false]]],
            ['q' => 'Quantas vezes Naamã se banhou no Jordão para ser curado?', 'category' => 'numeros', 'a' => [['text' => '7', 'correct' => true], ['text' => '3', 'correct' => false], ['text' => '12', 'correct' => false], ['text' => '1', 'correct' => false]]],
            ['q' => 'Em quantos dias Deus criou o mundo?', 'category' => 'numeros', 'a' => [['text' => '6', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '1', 'correct' => false], ['text' => '40', 'correct' => false]]],
            ['q' => 'Quantos anos Matusalém viveu?', 'category' => 'numeros', 'a' => [['text' => '969', 'correct' => true], ['text' => '900', 'correct' => false], ['text' => '120', 'correct' => false], ['text' => '777', 'correct' => false]]],

            // Eventos
            ['q' => 'Qual foi o primeiro milagre de Jesus?', 'category' => 'eventos', 'a' => [['text' => 'Transformar água em vinho', 'correct' => true], ['text' => 'Curar um leproso', 'correct' => false], ['text' => 'Multiplicar pães', 'correct' => false], ['text' => 'Ressuscitar Lázaro', 'correct' => false]]],
            ['q' => 'O que aconteceu no dia de Pentecostes?', 'category' => 'eventos', 'a' => [['text' => 'Descida do Espírito Santo', 'correct' => true], ['text' => 'Ressurreição de Jesus', 'correct' => false], ['text' => 'Ascensão de Jesus', 'correct' => false], ['text' => 'Nascimento da igreja', 'correct' => false]]],
            ['q' => 'O que caiu sobre Sodoma e Gomorra?', 'category' => 'eventos', 'a' => [['text' => 'Fogo e enxofre', 'correct' => true], ['text' => 'Dilúvio', 'correct' => false], ['text' => 'Pragas', 'correct' => false], ['text' => 'Trevas', 'correct' => false]]],
            ['q' => 'O que aconteceu quando as muralhas de Jericó caíram?', 'category' => 'eventos', 'a' => [['text' => 'Os israelitas tocaram trombetas', 'correct' => true], ['text' => 'Houve um terremoto', 'correct' => false], ['text' => 'Um anjo derrubou', 'correct' => false], ['text' => 'Moisés ordenou', 'correct' => false]]],
            ['q' => 'O que Jesus fez antes de ser preso?', 'category' => 'eventos', 'a' => [['text' => 'Orou no Getsêmani', 'correct' => true], ['text' => 'Pregou no templo', 'correct' => false], ['text' => 'Curou um cego', 'correct' => false], ['text' => 'Multiplicou pães', 'correct' => false]]],
            ['q' => 'O que aconteceu três dias após a crucificação?', 'category' => 'eventos', 'a' => [['text' => 'Jesus ressuscitou', 'correct' => true], ['text' => 'Jesus subiu ao céu', 'correct' => false], ['text' => 'O Espírito Santo desceu', 'correct' => false], ['text' => 'Pedro pregou', 'correct' => false]]],
            ['q' => 'O que aconteceu na transfiguração?', 'category' => 'eventos', 'a' => [['text' => 'Moisés e Elias apareceram', 'correct' => true], ['text' => 'Jesus foi batizado', 'correct' => false], ['text' => 'Jesus multiplicou pães', 'correct' => false], ['text' => 'Jesus ressuscitou', 'correct' => false]]],
            ['q' => 'O que aconteceu quando Paulo pregou em Atenas?', 'category' => 'eventos', 'a' => [['text' => 'Falou no Areópago', 'correct' => true], ['text' => 'Foi preso', 'correct' => false], ['text' => 'Converteu toda a cidade', 'correct' => false], ['text' => 'Foi apedrejado', 'correct' => false]]],
            ['q' => 'O que aconteceu quando Pedro pregou no Pentecostes?', 'category' => 'eventos', 'a' => [['text' => '3000 se converteram', 'correct' => true], ['text' => 'Foi preso', 'correct' => false], ['text' => 'Fez milagres', 'correct' => false], ['text' => '500 creram', 'correct' => false]]],
            ['q' => 'O que Jesus fez na Última Ceia?', 'category' => 'eventos', 'a' => [['text' => 'Instituiu a Santa Ceia', 'correct' => true], ['text' => 'Multiplicou pães', 'correct' => false], ['text' => 'Lavou os pés dos discípulos', 'correct' => false], ['text' => 'Todas as anteriores', 'correct' => false]]],

            // Parábolas
            ['q' => 'Na parábola do semeador, qual solo produziu fruto?', 'category' => 'parabolas', 'a' => [['text' => 'Terra boa', 'correct' => true], ['text' => 'Beira do caminho', 'correct' => false], ['text' => 'Solo pedregoso', 'correct' => false], ['text' => 'Entre espinhos', 'correct' => false]]],
            ['q' => 'Quantos talentos o servo recebeu e enterrou?', 'category' => 'parabolas', 'a' => [['text' => '1', 'correct' => true], ['text' => '2', 'correct' => false], ['text' => '5', 'correct' => false], ['text' => '10', 'correct' => false]]],
            ['q' => 'Quem ajudou o homem na parábola do Bom Samaritano?', 'category' => 'parabolas', 'a' => [['text' => 'Um samaritano', 'correct' => true], ['text' => 'Um levita', 'correct' => false], ['text' => 'Um sacerdote', 'correct' => false], ['text' => 'Um fariseu', 'correct' => false]]],
            ['q' => 'O que o filho pródigo pediu ao pai?', 'category' => 'parabolas', 'a' => [['text' => 'Sua herança', 'correct' => true], ['text' => 'Um emprego', 'correct' => false], ['text' => 'Comida', 'correct' => false], ['text' => 'Perdão', 'correct' => false]]],
            ['q' => 'Quantas virgens estavam preparadas na parábola?', 'category' => 'parabolas', 'a' => [['text' => '5', 'correct' => true], ['text' => '10', 'correct' => false], ['text' => '7', 'correct' => false], ['text' => '3', 'correct' => false]]],
            ['q' => 'O que o grão de mostarda representa na parábola?', 'category' => 'parabolas', 'a' => [['text' => 'Reino de Deus', 'correct' => true], ['text' => 'Fé', 'correct' => false], ['text' => 'Igreja', 'correct' => false], ['text' => 'Palavra', 'correct' => false]]],
            ['q' => 'O que o homem encontrou escondido no campo?', 'category' => 'parabolas', 'a' => [['text' => 'Um tesouro', 'correct' => true], ['text' => 'Uma pérola', 'correct' => false], ['text' => 'Ouro', 'correct' => false], ['text' => 'Sementes', 'correct' => false]]],
            ['q' => 'Na parábola do trigo e joio, quando se faz a separação?', 'category' => 'parabolas', 'a' => [['text' => 'Na colheita', 'correct' => true], ['text' => 'Imediatamente', 'correct' => false], ['text' => 'Quando brota', 'correct' => false], ['text' => 'Nunca', 'correct' => false]]],
            ['q' => 'O que o fariseu fez na parábola do fariseu e publicano?', 'category' => 'parabolas', 'a' => [['text' => 'Orou orgulhosamente', 'correct' => true], ['text' => 'Orou humildemente', 'correct' => false], ['text' => 'Deu esmolas', 'correct' => false], ['text' => 'Chorou', 'correct' => false]]],
            ['q' => 'O que representam as ovelhas na parábola da ovelha perdida?', 'category' => 'parabolas', 'a' => [['text' => 'Pecadores', 'correct' => true], ['text' => 'Justos', 'correct' => false], ['text' => 'Anjos', 'correct' => false], ['text' => 'Discípulos', 'correct' => false]]],

            // Milagres
            ['q' => 'Quantos pães Jesus usou para alimentar 5000?', 'category' => 'milagres', 'a' => [['text' => '5', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '2', 'correct' => false], ['text' => '12', 'correct' => false]]],
            ['q' => 'Quem Jesus ressuscitou após 4 dias morto?', 'category' => 'milagres', 'a' => [['text' => 'Lázaro', 'correct' => true], ['text' => 'Filha de Jairo', 'correct' => false], ['text' => 'Filho da viúva', 'correct' => false], ['text' => 'Tabita', 'correct' => false]]],
            ['q' => 'Quantas cestas sobraram após alimentar 5000?', 'category' => 'milagres', 'a' => [['text' => '12', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '5', 'correct' => false], ['text' => '2', 'correct' => false]]],
            ['q' => 'O que Jesus fez para curar o cego de nascença?', 'category' => 'milagres', 'a' => [['text' => 'Passou lodo nos olhos', 'correct' => true], ['text' => 'Tocou os olhos', 'correct' => false], ['text' => 'Apenas falou', 'correct' => false], ['text' => 'Soprou', 'correct' => false]]],
            ['q' => 'Quem andou sobre as águas com Jesus?', 'category' => 'milagres', 'a' => [['text' => 'Pedro', 'correct' => true], ['text' => 'João', 'correct' => false], ['text' => 'Tiago', 'correct' => false], ['text' => 'André', 'correct' => false]]],
            ['q' => 'Quantos leprosos Jesus curou e apenas um voltou?', 'category' => 'milagres', 'a' => [['text' => '10', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '5', 'correct' => false], ['text' => '12', 'correct' => false]]],
            ['q' => 'O que Jesus acalmou com uma palavra?', 'category' => 'milagres', 'a' => [['text' => 'Tempestade', 'correct' => true], ['text' => 'Terremoto', 'correct' => false], ['text' => 'Fogo', 'correct' => false], ['text' => 'Demônios', 'correct' => false]]],
            ['q' => 'Quem tocou na orla das vestes de Jesus e foi curada?', 'category' => 'milagres', 'a' => [['text' => 'Mulher com hemorragia', 'correct' => true], ['text' => 'Maria Madalena', 'correct' => false], ['text' => 'Marta', 'correct' => false], ['text' => 'Viúva de Naim', 'correct' => false]]],
            ['q' => 'Quem foi curado na piscina de Betesda?', 'category' => 'milagres', 'a' => [['text' => 'Paralítico há 38 anos', 'correct' => true], ['text' => 'Cego de nascença', 'correct' => false], ['text' => 'Leproso', 'correct' => false], ['text' => 'Surdo', 'correct' => false]]],
            ['q' => 'Em qual casamento Jesus fez seu primeiro milagre?', 'category' => 'milagres', 'a' => [['text' => 'Casamento em Caná', 'correct' => true], ['text' => 'Casamento em Nazaré', 'correct' => false], ['text' => 'Casamento em Jerusalém', 'correct' => false], ['text' => 'Casamento em Belém', 'correct' => false]]],

            // Profecias
            ['q' => 'Qual profeta previu o nascimento de Jesus em Belém?', 'category' => 'profecias', 'a' => [['text' => 'Miquéias', 'correct' => true], ['text' => 'Isaías', 'correct' => false], ['text' => 'Jeremias', 'correct' => false], ['text' => 'Daniel', 'correct' => false]]],
            ['q' => 'Qual profeta falou sobre o Servo Sofredor?', 'category' => 'profecias', 'a' => [['text' => 'Isaías', 'correct' => true], ['text' => 'Jeremias', 'correct' => false], ['text' => 'Ezequiel', 'correct' => false], ['text' => 'Daniel', 'correct' => false]]],
            ['q' => 'Quem profetizou sobre os ossos secos?', 'category' => 'profecias', 'a' => [['text' => 'Ezequiel', 'correct' => true], ['text' => 'Isaías', 'correct' => false], ['text' => 'Jeremias', 'correct' => false], ['text' => 'Daniel', 'correct' => false]]],
            ['q' => 'Qual profeta interpretou os sonhos de Nabucodonosor?', 'category' => 'profecias', 'a' => [['text' => 'Daniel', 'correct' => true], ['text' => 'Ezequiel', 'correct' => false], ['text' => 'Isaías', 'correct' => false], ['text' => 'José', 'correct' => false]]],
            ['q' => 'Quem profetizou a volta do profeta Elias?', 'category' => 'profecias', 'a' => [['text' => 'Malaquias', 'correct' => true], ['text' => 'Isaías', 'correct' => false], ['text' => 'Jeremias', 'correct' => false], ['text' => 'Amós', 'correct' => false]]],
            ['q' => 'O que Isaías disse que uma virgem conceberia?', 'category' => 'profecias', 'a' => [['text' => 'Emanuel', 'correct' => true], ['text' => 'Jesus', 'correct' => false], ['text' => 'Salvador', 'correct' => false], ['text' => 'Messias', 'correct' => false]]],
            ['q' => 'Quem previu os 70 anos de cativeiro babilônico?', 'category' => 'profecias', 'a' => [['text' => 'Jeremias', 'correct' => true], ['text' => 'Isaías', 'correct' => false], ['text' => 'Ezequiel', 'correct' => false], ['text' => 'Daniel', 'correct' => false]]],
            ['q' => 'Qual profeta teve a visão das quatro bestas?', 'category' => 'profecias', 'a' => [['text' => 'Daniel', 'correct' => true], ['text' => 'Ezequiel', 'correct' => false], ['text' => 'João', 'correct' => false], ['text' => 'Isaías', 'correct' => false]]],
            ['q' => 'Quem profetizou sobre o derramamento do Espírito?', 'category' => 'profecias', 'a' => [['text' => 'Joel', 'correct' => true], ['text' => 'Isaías', 'correct' => false], ['text' => 'Ezequiel', 'correct' => false], ['text' => 'Malaquias', 'correct' => false]]],
            ['q' => 'Qual profeta comparou Israel a uma esposa infiel?', 'category' => 'profecias', 'a' => [['text' => 'Oséias', 'correct' => true], ['text' => 'Amós', 'correct' => false], ['text' => 'Miquéias', 'correct' => false], ['text' => 'Naum', 'correct' => false]]],

            // Doutrina
            ['q' => 'Qual é o maior mandamento segundo Jesus?', 'category' => 'doutrina', 'a' => [['text' => 'Amar a Deus', 'correct' => true], ['text' => 'Não matar', 'correct' => false], ['text' => 'Não roubar', 'correct' => false], ['text' => 'Honrar pai e mãe', 'correct' => false]]],
            ['q' => 'Quantos frutos do Espírito são mencionados em Gálatas?', 'category' => 'doutrina', 'a' => [['text' => '9', 'correct' => true], ['text' => '7', 'correct' => false], ['text' => '12', 'correct' => false], ['text' => '10', 'correct' => false]]],
            ['q' => 'Qual é o primeiro fruto do Espírito listado?', 'category' => 'doutrina', 'a' => [['text' => 'Amor', 'correct' => true], ['text' => 'Alegria', 'correct' => false], ['text' => 'Paz', 'correct' => false], ['text' => 'Bondade', 'correct' => false]]],
            ['q' => 'O que significa "graça" na Bíblia?', 'category' => 'doutrina', 'a' => [['text' => 'Favor imerecido', 'correct' => true], ['text' => 'Recompensa', 'correct' => false], ['text' => 'Pagamento', 'correct' => false], ['text' => 'Bênção', 'correct' => false]]],
            ['q' => 'Qual versículo diz: "Porque Deus amou o mundo"?', 'category' => 'doutrina', 'a' => [['text' => 'João 3:16', 'correct' => true], ['text' => 'Romanos 8:28', 'correct' => false], ['text' => 'Salmo 23:1', 'correct' => false], ['text' => 'Filipenses 4:13', 'correct' => false]]],
            ['q' => 'Pela graça sois salvos, mediante a...?', 'category' => 'doutrina', 'a' => [['text' => 'Fé', 'correct' => true], ['text' => 'Lei', 'correct' => false], ['text' => 'Obras', 'correct' => false], ['text' => 'Igreja', 'correct' => false]]],
            ['q' => 'O que significa "justificação"?', 'category' => 'doutrina', 'a' => [['text' => 'Ser declarado justo', 'correct' => true], ['text' => 'Ser santo', 'correct' => false], ['text' => 'Ser batizado', 'correct' => false], ['text' => 'Ser perdoado', 'correct' => false]]],
            ['q' => 'Qual é o salário do pecado?', 'category' => 'doutrina', 'a' => [['text' => 'Morte', 'correct' => true], ['text' => 'Sofrimento', 'correct' => false], ['text' => 'Pobreza', 'correct' => false], ['text' => 'Doença', 'correct' => false]]],
            ['q' => 'O que é a santificação?', 'category' => 'doutrina', 'a' => [['text' => 'Processo de se tornar santo', 'correct' => true], ['text' => 'Salvação', 'correct' => false], ['text' => 'Batismo', 'correct' => false], ['text' => 'Conversão', 'correct' => false]]],
            ['q' => 'Quem é o Consolador prometido por Jesus?', 'category' => 'doutrina', 'a' => [['text' => 'Espírito Santo', 'correct' => true], ['text' => 'Anjo', 'correct' => false], ['text' => 'Pedro', 'correct' => false], ['text' => 'Paulo', 'correct' => false]]],

            // Mais perguntas variadas
            ['q' => 'Qual era a profissão de Pedro antes de seguir Jesus?', 'category' => 'personagens', 'a' => [['text' => 'Pescador', 'correct' => true], ['text' => 'Carpinteiro', 'correct' => false], ['text' => 'Cobrador de impostos', 'correct' => false], ['text' => 'Pastor', 'correct' => false]]],
            ['q' => 'Qual era a profissão de Mateus?', 'category' => 'personagens', 'a' => [['text' => 'Cobrador de impostos', 'correct' => true], ['text' => 'Pescador', 'correct' => false], ['text' => 'Médico', 'correct' => false], ['text' => 'Tenteiro', 'correct' => false]]],
            ['q' => 'Quem escreveu a maior parte das cartas do Novo Testamento?', 'category' => 'livros', 'a' => [['text' => 'Paulo', 'correct' => true], ['text' => 'Pedro', 'correct' => false], ['text' => 'João', 'correct' => false], ['text' => 'Tiago', 'correct' => false]]],
            ['q' => 'Qual o menor livro da Bíblia?', 'category' => 'livros', 'a' => [['text' => '2 João', 'correct' => true], ['text' => '3 João', 'correct' => false], ['text' => 'Obadias', 'correct' => false], ['text' => 'Filemom', 'correct' => false]]],
            ['q' => 'Qual o maior livro da Bíblia?', 'category' => 'livros', 'a' => [['text' => 'Salmos', 'correct' => true], ['text' => 'Isaías', 'correct' => false], ['text' => 'Jeremias', 'correct' => false], ['text' => 'Gênesis', 'correct' => false]]],
            ['q' => 'Em qual montanha Noé pousou a arca?', 'category' => 'lugares', 'a' => [['text' => 'Monte Ararate', 'correct' => true], ['text' => 'Monte Sinai', 'correct' => false], ['text' => 'Monte Carmelo', 'correct' => false], ['text' => 'Monte das Oliveiras', 'correct' => false]]],
            ['q' => 'Onde Elias desafiou os profetas de Baal?', 'category' => 'lugares', 'a' => [['text' => 'Monte Carmelo', 'correct' => true], ['text' => 'Monte Sinai', 'correct' => false], ['text' => 'Monte Horebe', 'correct' => false], ['text' => 'Monte Tabor', 'correct' => false]]],
            ['q' => 'Quantos anos viveu Adão?', 'category' => 'numeros', 'a' => [['text' => '930', 'correct' => true], ['text' => '969', 'correct' => false], ['text' => '900', 'correct' => false], ['text' => '950', 'correct' => false]]],
            ['q' => 'Quantos filhos Jacó teve?', 'category' => 'numeros', 'a' => [['text' => '12', 'correct' => true], ['text' => '10', 'correct' => false], ['text' => '13', 'correct' => false], ['text' => '7', 'correct' => false]]],
            ['q' => 'Quantos peixes multiplicados junto com pães?', 'category' => 'numeros', 'a' => [['text' => '2', 'correct' => true], ['text' => '5', 'correct' => false], ['text' => '7', 'correct' => false], ['text' => '3', 'correct' => false]]],
        ];

        foreach ($questions as $q) {
            $question = GameQuestion::firstOrCreate([
                'game_id' => $quiz->id,
                'question_text' => $q['q']
            ], [
                'points' => 10,
                'time_limit' => 30,
                'category' => $q['category'] ?? null,
            ]);

            if ($question->wasRecentlyCreated) {
                foreach ($q['a'] as $ans) {
                    GameAnswer::create([
                        'question_id' => $question->id,
                        'answer_text' => $ans['text'],
                        'is_correct' => $ans['correct'],
                    ]);
                }
            }
        }
    }
}
