<?php

namespace Modules\Worship\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Worship\App\Models\WorshipSong;

class WorshipSongsSeeder extends Seeder
{
    public function run()
    {
        $songs = [
            [
                'title' => 'Lugar Secreto',
                'artist' => 'Gabriela Rocha',
                'bpm' => 70,
                'time_signature' => '4/4',
                'original_key' => 'A',
                'youtube_link' => 'https://www.youtube.com/watch?v=0M7_GZgXj6o',
                'content' => '{title: Lugar Secreto}
{artist: Gabriela Rocha}
{key: A}
{time: 4/4}

[Intro]
[A  D/A  E/A] (2x)

[Verse 1]
[A]Tu és tudo o que eu [D]mais quero
[A]O meu fôlego, Tu [D]és
[F#m]Em Teus braços, é o [E]meu lugar
[D]Estou aqui, es[E]tou aqui

[Verse 2]
[A]Pai, eu amo Sua [D]presença
[A]Teu sorriso é [D]vida em mim
[F#m]Eu seguro em Suas [E]mãos
[D]Confio em Ti, con[E]fio em Ti

[Chorus]
Quero ir mais [A]fundo
Leva-me mais [F#m]perto
Onde eu Te en[E]contro
No lugar se[D]creto
Aos Teus [A]pés, me rendo
Pois a Tua [F#m]glória
Quero ver fa[E]ce a face
Te ver face a fa[D]ce

[Bridge]
[Bm]Tudo o que eu [A/C#]mais quero é Te [D]ver
Me en[E]volva com Tua [F#m]glória e po[E]der
Tua ma[D]jestade é re[E]al
Tua [F#m]voz ecoa em meu [E]ser',
            ],
            [
                'title' => 'Caminho no Deserto',
                'artist' => 'Soraya Moraes',
                'bpm' => 68,
                'time_signature' => '4/4',
                'original_key' => 'G',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: Caminho no Deserto}
{artist: Soraya Moraes}
{key: G}

[Intro]
[G  D/F#  Em  C]

[Verse 1]
[G]Estás aqui movendo entre [D]nós
Te ado[Em]rarei, Te ado[C]rarei
[G]Estás aqui mudando desti[D]nos
Te ado[Em]rarei, Te ado[C]rarei

[Chorus]
[G]Deus de promessas, [D]caminho no deserto
[Em]Luz na escuridão, [C]meu Deus, esse é quem Tu és
[G]Deus de promessas, [D]caminho no deserto
[Em]Luz na escuridão, [C]meu Deus, esse é quem Tu és',
            ],
            [
                'title' => 'Ousado Amor',
                'artist' => 'Isaias Saad',
                'bpm' => 74,
                'time_signature' => '6/8',
                'original_key' => 'F#',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: Ousado Amor}
{artist: Isaias Saad}
{key: F#}

[Intro]
[F#  C#/F  D#m  B]

[Verse 1]
[F#]Antes de eu falar
[C#/F]Tu cantavas sobre [D#m]mim [B]
[F#]Tu tens sido tão, [C#/F]tão bom pra [D#m]mim [B]

[Chorus]
[F#]Oh, impressionante, infini[C#/F]to e ousado amor de [D#m]Deus [B]
[F#]Oh, que deixa as noventa e no[C#/F]ve só pra me encon[D#m]trar [B]
Não posso comprá-[F#]lo, nem mere[C#/F]cê-lo
Mesmo assim se entre[D#m]gou [B]
[F#]Oh, impressionante, infini[C#/F]to e ousado amor de [D#m]Deus [B]',
            ],
            [
                'title' => 'A Casa É Sua',
                'artist' => 'Casa Worship',
                'bpm' => 72,
                'time_signature' => '4/4',
                'original_key' => 'A',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: A Casa É Sua}
{artist: Casa Worship}
{key: A}

[Intro]
[F#m  D  A  E]

[Verse 1]
[F#m]Você é bem-vindo a[D]qui
[A]A casa é [E]Sua, pode en[F#m]trar
Me esvazi[D]ei de mim
[A]Sopra Teu [E]vento aqui

[Chorus]
[F#m]Essa casa é [D]Sua casa
[A]Nós deixamos [E]ela pra Você, Je[F#m]sus
[D]Essa casa é Sua ca[A]sa
Nós deixamos [E]ela pra Você, Je[F#m]sus

[Bridge]
[D]Apareça, [A]que o Teu nome cresça
Enche este lu[E]gar, enche este lu[F#m]gar
[D]Apareça, [A]que o Teu nome cresça
Enche este lu[E]gar, enche este lu[F#m]gar',
            ],
            [
                'title' => 'Porque Ele Vive',
                'artist' => 'Harpa Cristã',
                'bpm' => 80,
                'time_signature' => '4/4',
                'original_key' => 'G',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: Porque Ele Vive}
{artist: Harpa Cristã}
{key: G}

[Intro]
[G  C  G  D  G]

[Verse 1]
[G]Deus enviou [G7]seu Filho a[C]mado
Para perdo[G]ar, pra me sal[D]var
[G]Na cruz mor[G7]reu por meus pe[C]cados
Mas ressur[G]giu e vivo [D]com o Pai es[G]tá

[Chorus]
Porque Ele [C]vive, posso crer no ama[G]nhã
Porque Ele [C]vive, temor não [D]há
Mas eu bem [G]sei, eu [G7]sei, que a minha [C]vida
Está nas [G]mãos do meu Je[D]sus, que vivo es[G]tá',
            ],
            [
                'title' => 'Em Teus Braços',
                'artist' => 'Laura Souguellis',
                'bpm' => 64,
                'time_signature' => '4/4',
                'original_key' => 'F',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: Em Teus Braços}
{artist: Laura Souguellis}
{key: F}

[Intro]
[Dm  Bb  F  C]

[Verse 1]
[Dm]Segura estou nos braços [Bb]Daquele que nunca me dei[F]xou
[C]Seu amor perfeito sempre es[Dm]teve
Repou[Bb]sado em [F]mim [C]

[Chorus]
E se eu pas[Dm]sar pelo vale
Acha[Bb]rei conforto em Teu a[F]mor
Pois eu [C]sei que és Aquele
que me [Dm]guarda, me [Bb]guardas
Em Teus [F]braços é meu des[C]canso',
            ],
            [
                'title' => 'Maranata',
                'artist' => 'Ministério Avivah',
                'bpm' => 76,
                'time_signature' => '4/4',
                'original_key' => 'D',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: Maranata}
{artist: Ministério Avivah}
{key: D}

[Intro]
[Bm  G  D  A]

[Verse 1]
[Bm]Tu és a minha luz
[G]A minha salvação
[D]E a Ti me ren[A]derei
[Bm]Se estais comigo
[G]A quem temerei?
[D]O meu louvor é [A]só Teu

[Chorus]
[Bm]Maranata, [G]ora vem Senhor Jesus
[D]Maranata, [A]ora vem Senhor Jesus',
            ],
            [
                'title' => 'Que Se Abram os Céus',
                'artist' => 'Nívea Soares',
                'bpm' => 70,
                'time_signature' => '4/4',
                'original_key' => 'E',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: Que Se Abram os Céus}
{artist: Nívea Soares}
{key: E}

[Intro]
[E  B  C#m  A]

[Verse 1]
[E]Tu és bem-vindo a[B]qui
[C#m]Tua glória ve[A]mos
[E]Ao Teu povo encon[B]trar
[C#m]Teu amor flui[A]rá

[Chorus]
[E]Que se abram os céus
[B]O Teu reino vem
[C#m]Nossa fé se levanta
[A]Nossa fé se levanta',
            ],
            [
                'title' => 'Pra Sempre',
                'artist' => 'Fernandinho',
                'bpm' => 72,
                'time_signature' => '4/4',
                'original_key' => 'C',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: Pra Sempre}
{artist: Fernandinho}
{key: C}

[Intro]
[C  G  Am  F]

[Verse 1]
[C]O universo chora
[G]O sol se apagou
[Am]Ali estava morto
[F]O Salvador

[Chorus]
[C]Pra sempre exaltado é
[G]Pra sempre adorado é
[Am]Pra sempre Ele vive
[F]Ressuscitou, ressuscitou',
            ],
            [
                'title' => 'So Quero Ver Voce',
                'artist' => 'Filipe Hitzschky',
                'bpm' => 68,
                'time_signature' => '4/4',
                'original_key' => 'D',
                'youtube_link' => 'https://www.youtube.com/watch?v=VIDEO_ID',
                'content' => '{title: So Quero Ver Voce}
{artist: Filipe Hitzschky}
{key: D}

[Intro]
[Bm  A  G]

[Verse 1]
[Bm]O que os anjos veem
[A]Que os fazem se prostrar?
[G]O que os anjos veem
[D/F#]Que os fazem cantar?

[Chorus]
[Bm]Eu só quero ver Você
[A]Eu só quero ver Você
[G]Eu só quero ver Você
[D/F#]Eu só quero ver Você',
            ],
        ];

        foreach ($songs as $song) {
            WorshipSong::firstOrCreate(
                ['title' => $song['title'], 'artist' => $song['artist']],
                $song
            );
        }
    }
}
