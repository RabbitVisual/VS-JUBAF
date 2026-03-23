<?php

namespace Modules\Worship\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Worship\App\Models\WorshipSong;
use Modules\Worship\App\Enums\MusicalKey;

class ConvertKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worship:convert-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy string musical keys to Enum format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting conversion of musical keys...');

        $songs = WorshipSong::all();
        $count = 0;

        foreach ($songs as $song) {
            $currentKey = $song->getRawOriginal('original_key');

            // If it's already an enum or empty, skip
            if (!$currentKey || $currentKey instanceof MusicalKey) {
                continue;
            }

            // Normalization mapping if needed
            $map = [
                'Db' => 'C#',
                'Eb' => 'D#',
                'Gb' => 'F#',
                'Ab' => 'G#',
                'Bb' => 'A#',
            ];

            if (isset($map[$currentKey])) {
                $currentKey = $map[$currentKey];
            }

            try {
                $song->original_key = $currentKey;
                $song->save();
                $count++;
            } catch (\Exception $e) {
                $this->warn("Could not convert key '{$currentKey}' for song ID: {$song->id}");
            }
        }

        $this->info("Successfully converted {$count} songs.");
        return 0;
    }
}
