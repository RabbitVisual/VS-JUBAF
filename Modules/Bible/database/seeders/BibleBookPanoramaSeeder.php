<?php

namespace Modules\Bible\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Bible\App\Models\BibleBookPanorama;

class BibleBookPanoramaSeeder extends Seeder
{
    /**
     * Panorama resumido por livro canônico (1-66). Fontes: consenso histórico/Isaltino.
     * language = pt. Expandir theme_central e recipients conforme necessidade.
     */
    public function run(): void
    {
        $data = [
            ['book_number' => 1, 'testament' => 'old', 'author' => 'Moisés', 'date_written' => 'c. 1445-1405 a.C.', 'theme_central' => 'Origem do mundo, do povo de Israel e da promessa redentora.', 'recipients' => 'Israel e as gerações futuras.'],
            ['book_number' => 2, 'testament' => 'old', 'author' => 'Moisés', 'date_written' => 'c. 1445-1405 a.C.', 'theme_central' => 'Libertação do Egito e aliança no Sinai.', 'recipients' => 'Israel.'],
            ['book_number' => 3, 'testament' => 'old', 'author' => 'Moisés', 'date_written' => 'c. 1445-1405 a.C.', 'theme_central' => 'Lei sacerdotal e santidade.', 'recipients' => 'Israel.'],
            ['book_number' => 4, 'testament' => 'old', 'author' => 'Moisés', 'date_written' => 'c. 1405 a.C.', 'theme_central' => 'Jornada no deserto e preparação para Canaã.', 'recipients' => 'Israel.'],
            ['book_number' => 5, 'testament' => 'old', 'author' => 'Moisés', 'date_written' => 'c. 1405 a.C.', 'theme_central' => 'Repetição da lei e exortação à fidelidade.', 'recipients' => 'Israel na planície de Moabe.'],
            ['book_number' => 6, 'testament' => 'old', 'author' => 'Josué (trad.)', 'date_written' => 'c. 1375 a.C.', 'theme_central' => 'Conquista e divisão de Canaã.', 'recipients' => 'Israel.'],
            ['book_number' => 7, 'testament' => 'old', 'author' => 'Samuel ou profeta (trad.)', 'date_written' => 'c. 1050-1000 a.C.', 'theme_central' => 'Ciclos de apostasia, opressão e libertação.', 'recipients' => 'Israel.'],
            ['book_number' => 8, 'testament' => 'old', 'author' => 'Samuel; Gade; Natã (trad.)', 'date_written' => 'c. 970-930 a.C.', 'theme_central' => 'Reinado de Davi e promessa dinástica.', 'recipients' => 'Israel.'],
            ['book_number' => 9, 'testament' => 'old', 'author' => 'Indeterminado (trad. Esdras)', 'date_written' => 'c. 450 a.C.', 'theme_central' => 'História dos reis de Judá e Israel.', 'recipients' => 'Povo pós-exílico.'],
            ['book_number' => 10, 'testament' => 'old', 'author' => 'Indeterminado (trad. Esdras)', 'date_written' => 'c. 450 a.C.', 'theme_central' => 'Continuação da história real.', 'recipients' => 'Povo pós-exílico.'],
            ['book_number' => 11, 'testament' => 'old', 'author' => 'Indeterminado (trad. Esdras)', 'date_written' => 'c. 560-540 a.C.', 'theme_central' => 'Salomão, reino dividido e exílio.', 'recipients' => 'Israel/Judá.'],
            ['book_number' => 12, 'testament' => 'old', 'author' => 'Indeterminado (trad. Esdras)', 'date_written' => 'c. 560-540 a.C.', 'theme_central' => 'Reinos de Judá e Israel.', 'recipients' => 'Israel/Judá.'],
            ['book_number' => 13, 'testament' => 'old', 'author' => 'Esdras (trad.)', 'date_written' => 'c. 450 a.C.', 'theme_central' => 'Retorno do exílio e reconstrução.', 'recipients' => 'Povo pós-exílico.'],
            ['book_number' => 14, 'testament' => 'old', 'author' => 'Neemias / Esdras (trad.)', 'date_written' => 'c. 430 a.C.', 'theme_central' => 'Reconstrução dos muros e reforma.', 'recipients' => 'Judeus em Jerusalém.'],
            ['book_number' => 15, 'testament' => 'old', 'author' => 'Esdras (trad.)', 'date_written' => 'c. 450 a.C.', 'theme_central' => 'Providência de Deus na história do povo.', 'recipients' => 'Povo pós-exílico.'],
            ['book_number' => 16, 'testament' => 'old', 'author' => 'Neemias (trad.)', 'date_written' => 'c. 430 a.C.', 'theme_central' => 'Dedicação e reforma em Jerusalém.', 'recipients' => 'Judeus.'],
            ['book_number' => 17, 'testament' => 'old', 'author' => 'Jó (narrador desconhecido)', 'date_written' => 'Indeterminado (antigo)', 'theme_central' => 'Sofrimento, justiça e soberania de Deus.', 'recipients' => 'Geral.'],
            ['book_number' => 18, 'testament' => 'old', 'author' => 'Davi e outros', 'date_written' => 'c. 1000-400 a.C.', 'theme_central' => 'Oração, louvor e confiança em Deus.', 'recipients' => 'Israel e adoradores.'],
            ['book_number' => 19, 'testament' => 'old', 'author' => 'Davi e outros', 'date_written' => 'c. 1000-400 a.C.', 'theme_central' => 'Sabedoria e meditação na lei.', 'recipients' => 'Geral.'],
            ['book_number' => 20, 'testament' => 'old', 'author' => 'Salomão e outros', 'date_written' => 'c. 950-700 a.C.', 'theme_central' => 'Sabedoria prática e temor do Senhor.', 'recipients' => 'Israel e reis.'],
            ['book_number' => 21, 'testament' => 'old', 'author' => 'Salomão (trad.)', 'date_written' => 'c. 935 a.C.', 'theme_central' => 'Vaidade das realizações sem Deus.', 'recipients' => 'Geral.'],
            ['book_number' => 22, 'testament' => 'old', 'author' => 'Salomão (trad.)', 'date_written' => 'c. 970-930 a.C.', 'theme_central' => 'Amor conjugal e aliança.', 'recipients' => 'Israel.'],
            ['book_number' => 23, 'testament' => 'old', 'author' => 'Isaías', 'date_written' => 'c. 700-680 a.C.', 'theme_central' => 'Juízo, redenção e reino messiânico.', 'recipients' => 'Judá e Jerusalém.'],
            ['book_number' => 24, 'testament' => 'old', 'author' => 'Jeremias', 'date_written' => 'c. 626-586 a.C.', 'theme_central' => 'Arrependimento, exílio e nova aliança.', 'recipients' => 'Judá.'],
            ['book_number' => 25, 'testament' => 'old', 'author' => 'Jeremias', 'date_written' => 'c. 586 a.C.', 'theme_central' => 'Lamento pela queda de Jerusalém.', 'recipients' => 'Judeus no exílio.'],
            ['book_number' => 26, 'testament' => 'old', 'author' => 'Ezequiel', 'date_written' => 'c. 593-571 a.C.', 'theme_central' => 'Gloria de Deus, juízo e restauração.', 'recipients' => 'Exilados na Babilônia.'],
            ['book_number' => 27, 'testament' => 'old', 'author' => 'Daniel', 'date_written' => 'c. 605-536 a.C.', 'theme_central' => 'Soberania de Deus e reinos futuros.', 'recipients' => 'Exilados e reis.'],
            ['book_number' => 28, 'testament' => 'old', 'author' => 'Oséias', 'date_written' => 'c. 755-715 a.C.', 'theme_central' => 'Fidelidade de Deus apesar da infidelidade de Israel.', 'recipients' => 'Israel (Norte).'],
            ['book_number' => 29, 'testament' => 'old', 'author' => 'Joel', 'date_written' => 'c. 840 ou 400 a.C.', 'theme_central' => 'Dia do Senhor e derramamento do Espírito.', 'recipients' => 'Judá.'],
            ['book_number' => 30, 'testament' => 'old', 'author' => 'Amós', 'date_written' => 'c. 760-750 a.C.', 'theme_central' => 'Justiça social e juízo.', 'recipients' => 'Israel (Norte).'],
            ['book_number' => 31, 'testament' => 'old', 'author' => 'Obadias', 'date_written' => 'c. 586 ou 845 a.C.', 'theme_central' => 'Juízo sobre Edom e triunfo de Sião.', 'recipients' => 'Judá.'],
            ['book_number' => 32, 'testament' => 'old', 'author' => 'Jonas', 'date_written' => 'c. 780-760 a.C.', 'theme_central' => 'Misericórdia de Deus para com as nações.', 'recipients' => 'Israel e Nínive.'],
            ['book_number' => 33, 'testament' => 'old', 'author' => 'Miquéias', 'date_written' => 'c. 735-700 a.C.', 'theme_central' => 'Justiça, misericórdia e rei em Belém.', 'recipients' => 'Judá e Samaria.'],
            ['book_number' => 34, 'testament' => 'old', 'author' => 'Naum', 'date_written' => 'c. 650-620 a.C.', 'theme_central' => 'Queda de Nínive e consolo de Judá.', 'recipients' => 'Judá.'],
            ['book_number' => 35, 'testament' => 'old', 'author' => 'Habacuque', 'date_written' => 'c. 609-598 a.C.', 'theme_central' => 'Fé em Deus em meio ao mal.', 'recipients' => 'Judá.'],
            ['book_number' => 36, 'testament' => 'old', 'author' => 'Sofonias', 'date_written' => 'c. 640-621 a.C.', 'theme_central' => 'Dia do Senhor e restauração.', 'recipients' => 'Judá.'],
            ['book_number' => 37, 'testament' => 'old', 'author' => 'Ageu', 'date_written' => 'c. 520 a.C.', 'theme_central' => 'Reconstrução do templo e prioridades.', 'recipients' => 'Judeus em Jerusalém.'],
            ['book_number' => 38, 'testament' => 'old', 'author' => 'Zacarias', 'date_written' => 'c. 520-480 a.C.', 'theme_central' => 'Restauração e vinda do Messias.', 'recipients' => 'Judeus.'],
            ['book_number' => 39, 'testament' => 'old', 'author' => 'Malaquias', 'date_written' => 'c. 430 a.C.', 'theme_central' => 'Fidelidade a Deus e dia do Senhor.', 'recipients' => 'Sacerdotes e povo.'],
            ['book_number' => 40, 'testament' => 'new', 'author' => 'Mateus', 'date_written' => 'c. 50-65 d.C.', 'theme_central' => 'Jesus como Rei e cumprimento das promessas.', 'recipients' => 'Judeus e gentios.'],
            ['book_number' => 41, 'testament' => 'new', 'author' => 'Marcos', 'date_written' => 'c. 55-65 d.C.', 'theme_central' => 'Jesus servo, ação e cruz.', 'recipients' => 'Romanos (provavelmente).'],
            ['book_number' => 42, 'testament' => 'new', 'author' => 'Lucas', 'date_written' => 'c. 59-75 d.C.', 'theme_central' => 'Jesus Salvador de todos, histórico e universal.', 'recipients' => 'Teófilo e gentios.'],
            ['book_number' => 43, 'testament' => 'new', 'author' => 'João', 'date_written' => 'c. 85-95 d.C.', 'theme_central' => 'Jesus Filho de Deus e vida eterna.', 'recipients' => 'Igreja em geral.'],
            ['book_number' => 44, 'testament' => 'new', 'author' => 'Lucas', 'date_written' => 'c. 62 d.C.', 'theme_central' => 'Expansão da igreja pelo Espírito.', 'recipients' => 'Teófilo.'],
            ['book_number' => 45, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 57 d.C.', 'theme_central' => 'Justificação pela fé e vida no Espírito.', 'recipients' => 'Igreja em Roma.'],
            ['book_number' => 46, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 55 d.C.', 'theme_central' => 'Sabedoria de Deus, divisões e ressurreição.', 'recipients' => 'Corinto.'],
            ['book_number' => 47, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 56 d.C.', 'theme_central' => 'Reconciliação e ministério.', 'recipients' => 'Corinto.'],
            ['book_number' => 48, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 49 d.C.', 'theme_central' => 'Justificação pela fé e liberdade em Cristo.', 'recipients' => 'Igrejas da Galácia.'],
            ['book_number' => 49, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 60-62 d.C.', 'theme_central' => 'Unidade da igreja e andar em Cristo.', 'recipients' => 'Efésios.'],
            ['book_number' => 50, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 60-62 d.C.', 'theme_central' => 'Alegria em Cristo e contentamento.', 'recipients' => 'Filipenses.'],
            ['book_number' => 51, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 60-62 d.C.', 'theme_central' => 'Cristo preeminente e vida piedosa.', 'recipients' => 'Colossos.'],
            ['book_number' => 52, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 51 d.C.', 'theme_central' => 'Parousia e santificação.', 'recipients' => 'Tessalônica.'],
            ['book_number' => 53, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 51 d.C.', 'theme_central' => 'Clarificação sobre a volta de Cristo.', 'recipients' => 'Tessalônica.'],
            ['book_number' => 54, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 64 d.C.', 'theme_central' => 'Doutrina e ordem na igreja.', 'recipients' => 'Timóteo.'],
            ['book_number' => 55, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 64-67 d.C.', 'theme_central' => 'Fidelidade no ministério.', 'recipients' => 'Timóteo.'],
            ['book_number' => 56, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 63 d.C.', 'theme_central' => 'Doutrina sadia e boas obras.', 'recipients' => 'Tito.'],
            ['book_number' => 57, 'testament' => 'new', 'author' => 'Paulo', 'date_written' => 'c. 61 d.C.', 'theme_central' => 'Reconciliação e escravo fiel.', 'recipients' => 'Filemon.'],
            ['book_number' => 58, 'testament' => 'new', 'author' => 'Indeterminado (trad. Paulo)', 'date_written' => 'c. 68-70 d.C.', 'theme_central' => 'Cristo superior e nova aliança.', 'recipients' => 'Cristãos hebreus.'],
            ['book_number' => 59, 'testament' => 'new', 'author' => 'Tiago', 'date_written' => 'c. 45-50 d.C.', 'theme_central' => 'Fé e obras, sabedoria prática.', 'recipients' => 'Doze tribos na diáspora.'],
            ['book_number' => 60, 'testament' => 'new', 'author' => 'Pedro', 'date_written' => 'c. 62-64 d.C.', 'theme_central' => 'Esperança viva e santidade.', 'recipients' => 'Crentes na dispersão.'],
            ['book_number' => 61, 'testament' => 'new', 'author' => 'Pedro', 'date_written' => 'c. 64-68 d.C.', 'theme_central' => 'Conhecimento de Cristo e falsos mestres.', 'recipients' => 'Crentes.'],
            ['book_number' => 62, 'testament' => 'new', 'author' => 'João', 'date_written' => 'c. 90-95 d.C.', 'theme_central' => 'Comunhão com Deus e amor.', 'recipients' => 'Igreja em geral.'],
            ['book_number' => 63, 'testament' => 'new', 'author' => 'João', 'date_written' => 'c. 90-95 d.C.', 'theme_central' => 'Verdade e amor na igreja.', 'recipients' => 'Eleita e filhos.'],
            ['book_number' => 64, 'testament' => 'new', 'author' => 'João', 'date_written' => 'c. 90-95 d.C.', 'theme_central' => 'Verdade, amor e hospitalidade.', 'recipients' => 'Gaio.'],
            ['book_number' => 65, 'testament' => 'new', 'author' => 'Judas', 'date_written' => 'c. 65-80 d.C.', 'theme_central' => 'Contender pela fé e falsos mestres.', 'recipients' => 'Crentes.'],
            ['book_number' => 66, 'testament' => 'new', 'author' => 'João', 'date_written' => 'c. 90-96 d.C.', 'theme_central' => 'Vitória de Cristo e nova criação.', 'recipients' => 'Sete igrejas e igreja universal.'],
        ];

        foreach ($data as $row) {
            BibleBookPanorama::updateOrCreate(
                ['book_number' => $row['book_number'], 'language' => 'pt'],
                array_merge($row, ['language' => 'pt'])
            );
        }
    }
}
