<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Playlist;
use App\Models\VinylMaster;
use App\Models\PlaylistTrack;
use Illuminate\Support\Str;

class PlaylistSeeder extends Seeder
{
    public function run(): void
    {
        $playlists = [
            [
                'name' => 'Samba Raiz',
                'bio' => 'Uma seleção dos melhores sambas clássicos do Brasil, desde os anos 60 até hoje.',
                'instagram_url' => 'https://instagram.com/sambaraiz',
                'soundcloud_url' => 'https://soundcloud.com/sambaraiz',
                'is_active' => true,
            ],
            [
                'name' => 'MPB Essencial',
                'bio' => 'O melhor da Música Popular Brasileira em uma playlist cuidadosamente selecionada.',
                'instagram_url' => 'https://instagram.com/mpbessencial',
                'youtube_url' => 'https://youtube.com/mpbessencial',
                'is_active' => true,
            ],
            [
                'name' => 'Bossa Nova Collection',
                'bio' => 'Uma viagem pela Bossa Nova, com clássicos que marcaram época.',
                'instagram_url' => 'https://instagram.com/bossanova',
                'facebook_url' => 'https://facebook.com/bossanova',
                'is_active' => true,
            ],
            [
                'name' => 'Forró Pé de Serra',
                'bio' => 'O autêntico forró nordestino, com os maiores sucessos do ritmo.',
                'instagram_url' => 'https://instagram.com/forropedeserra',
                'youtube_url' => 'https://youtube.com/forropedeserra',
                'is_active' => true,
            ],
            [
                'name' => 'Axé Classics',
                'bio' => 'Os hits que fizeram história no Carnaval da Bahia.',
                'instagram_url' => 'https://instagram.com/axeclassics',
                'soundcloud_url' => 'https://soundcloud.com/axeclassics',
                'is_active' => true,
            ],
        ];

        foreach ($playlists as $playlistData) {
            $playlist = Playlist::create([
                'name' => $playlistData['name'],
                'slug' => Str::slug($playlistData['name']),
                'bio' => $playlistData['bio'],
                'instagram_url' => $playlistData['instagram_url'] ?? null,
                'youtube_url' => $playlistData['youtube_url'] ?? null,
                'facebook_url' => $playlistData['facebook_url'] ?? null,
                'soundcloud_url' => $playlistData['soundcloud_url'] ?? null,
                'is_active' => $playlistData['is_active'],
            ]);

            // Pega 10 vinis aleatórios para cada playlist
            $vinyls = VinylMaster::inRandomOrder()->limit(10)->get();
            
            foreach ($vinyls as $vinyl) {
                $playlist->adicionarFaixa($vinyl, $vinyl);
            }
        }
    }
}
