<?php

namespace Modules\Worship\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Worship\App\Models\WorshipSong;

class SyncLyricsCommand extends Command
{
    protected $signature = 'worship:sync-lyrics';
    protected $description = 'Regenerate lyrics_only for all songs from their ChordPro content';

    public function handle()
    {
        $songs = WorshipSong::all();
        $this->info("Regenerating lyrics for {$songs->count()} songs...");

        $bar = $this->output->createProgressBar($songs->count());
        $bar->start();

        foreach ($songs as $song) {
            $song->regenerateLyrics();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Lyrics synchronized successfully!');
    }
}
