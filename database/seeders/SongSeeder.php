<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Song;
use Illuminate\Support\Facades\DB;

class SongSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiamos la tabla antes de sembrar
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Song::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $discography = [
            // EP: ADRENALINE
            ['name' => 'Adrenaline', 'duration' => 278, 'date' => '2009-01-10', 'type' => 'ep', 'coll' => 'Adrenaline', 'order' => 1],
            ['name' => 'Live', 'duration' => 205, 'date' => '2009-01-10', 'type' => 'ep', 'coll' => 'Adrenaline', 'order' => 2],
            ['name' => 'Cynic', 'duration' => 217, 'date' => '2009-01-10', 'type' => 'ep', 'coll' => 'Adrenaline', 'order' => 3],
            ['name' => 'Heartbeat', 'duration' => 177, 'date' => '2009-01-10', 'type' => 'ep', 'coll' => 'Adrenaline', 'order' => 4],
            ['name' => 'Hypothermia', 'duration' => 217, 'date' => '2009-01-10', 'type' => 'ep', 'coll' => 'Adrenaline', 'order' => 5],
            ['name' => 'Pheromone', 'duration' => 211, 'date' => '2009-01-10', 'type' => 'ep', 'coll' => 'Adrenaline', 'order' => 6],
        
            // EP: FLUX
            ['name' => 'Flux', 'duration' => 355, 'date' => '2009-05-31', 'type' => 'ep', 'coll' => 'Flux', 'order' => 1],
            ['name' => 'Sundown', 'duration' => 318, 'date' => '2010-06-03', 'type' => 'ep', 'coll' => 'Flux', 'order' => 2],
            
            // SINGLE: MAKE IT LOOK LIKE AN ACCIDENT
            ['name' => 'Make It Look Like An Accident', 'duration' => 374, 'date' => '2011-06-23', 'type' => 'single'],

            // SINGLE: MALEDICTION
            ['name' => 'Malediction', 'duration' => 215, 'date' => '2012-03-27', 'type' => 'single'],

            // EP: MEET YOUR MAKER
            ['name' => 'Meet Your Maker', 'duration' => 263, 'date' => '2014-08-09', 'type' => 'ep', 'coll' => 'Meet Your Maker', 'order' => 1],
            ['name' => 'Apex', 'duration' => 252, 'date' => '2014-10-28', 'type' => 'ep', 'coll' => 'Meet Your Maker', 'order' => 2],
            
            // SINGLE: HALCYON
            ['name' => 'Halcyon', 'duration' => 320, 'date' => '2014-11-12', 'type' => 'single'],

            // SINGLE: START YOUR ENGINES
            ['name' => 'Start Your Engines', 'duration' => 230, 'date' => '2015-08-09', 'type' => 'single'],

            // SINGLES: PHANTASM
            ['name' => 'Phantasm', 'duration' => 355, 'date' => '2015-12-03', 'type' => 'single'],

            // EP: VIRTUALISM
            ['name' => 'Avalanche', 'duration' => 245, 'date' => '2016-06-17', 'type' => 'ep', 'coll' => 'Virtualism', 'order' => 1],
            ['name' => 'Summit', 'duration' => 199, 'date' => '2016-07-11', 'type' => 'ep', 'coll' => 'Virtualism', 'order' => 2],
            ['name' => 'Carnivores', 'duration' => 273, 'date' => '2016-07-11', 'type' => 'ep', 'coll' => 'Virtualism', 'order' => 3],
            ['name' => 'Vice', 'duration' => 228, 'date' => '2016-07-11', 'type' => 'ep', 'coll' => 'Virtualism', 'order' => 4],
            
            // SINGLE: 322
            ['name' => '322', 'duration' => 230, 'date' => '2016-10-24', 'type' => 'single'],

            // SINGLE: DIMENSION
            ['name' => 'Dimension', 'duration' => 193, 'date' => '2016-10-24', 'type' => 'single'],
            
            // SINGLE: MEMORY
            ['name' => 'Memory', 'duration' => 247, 'date' => '2017-01-02', 'type' => 'single'],
            
            // SINGLE: EPILOGUE
            ['name' => 'Epilogue', 'duration' => 276, 'date' => '2017-01-21', 'type' => 'single'],
            
            // EP: NEMOPHORE
            ['name' => 'Nemophore', 'duration' => 253, 'date' => '2017-03-04', 'type' => 'ep', 'coll' => 'Nemophore', 'order' => 1],
            ['name' => 'Rivals', 'duration' => 271, 'date' => '2017-03-04', 'type' => 'ep', 'coll' => 'Nemophore', 'order' => 2],
            ['name' => 'Challenger', 'duration' => 174, 'date' => '2017-03-04', 'type' => 'ep', 'coll' => 'Nemophore', 'order' => 3],

            // SINGLE: SPHERE
            ['name' => 'Sphere', 'duration' => 241, 'date' => '2017-04-17', 'type' => 'single'],

            // SINGLE: AHEAD OF THE CURVE
            ['name' => 'Ahead of the Curve', 'duration' => 202, 'date' => '2017-05-03', 'type' => 'single'],

            // SINGLE: SHAPE OF THE SUN
            ['name' => 'Shape of the Sun', 'duration' => 196, 'date' => '2017-06-22', 'type' => 'single'],

            // SINGLE: SHOWDOWN
            ['name' => 'Showdown', 'duration' => 199, 'date' => '2017-07-04', 'type' => 'single'],

            // EP: ODYSSEY
            ['name' => 'Dune', 'duration' => 211, 'date' => '2017-10-22', 'type' => 'ep', 'coll' => 'Odyssey', 'order' => 1],
            ['name' => 'Exoplanet', 'duration' => 247, 'date' => '2017-10-26', 'type' => 'ep', 'coll' => 'Odyssey', 'order' => 2],
            ['name' => 'Black Ice', 'duration' => 254, 'date' => '2017-10-26', 'type' => 'ep', 'coll' => 'Odyssey', 'order' => 3],
            ['name' => 'Enigma', 'duration' => 235, 'date' => '2017-10-26', 'type' => 'ep', 'coll' => 'Odyssey', 'order' => 4],

            // SINGLE: ROCK THING
            ['name' => 'Rock Thing', 'duration' => 204, 'date' => '2017-12-07', 'type' => 'single'],

            // SINGLE: GRAVITATE
            ['name' => 'Gravitate', 'duration' => 190, 'date' => '2018-01-25', 'type' => 'single'],
            
            // SINGLE: IN CIRCLES
            ['name' => 'In Circles', 'duration' => 243, 'date' => '2018-01-25', 'type' => 'single'],
            
            // SINGLE: SLOW DOWN
            ['name' => 'Slow Down', 'duration' => 237, 'date' => '2018-04-13', 'type' => 'single'],
            
            // SINGLE: CRYSTALLIZE
            ['name' => 'Crystallize', 'duration' => 201, 'date' => '2018-06-14', 'type' => 'single'],
            
            // SINGLE: OUTLAW
            ['name' => 'Outlaw', 'duration' => 304, 'date' => '2018-09-13', 'type' => 'single'],
            
            // SINGLE: IDOLIZE
            ['name' => 'Idolize', 'duration' => 237, 'date' => '2018-10-26', 'type' => 'single'],
            
            // SINGLE: NAUTILUS
            ['name' => 'Nautilus', 'duration' => 323, 'date' => '2018-10-26', 'type' => 'single'],
            
            // SINGLE: GLOME
            ['name' => 'Glome', 'duration' => 253, 'date' => '2018-12-20', 'type' => 'single'],
            
            // SINGLE: AURA
            ['name' => 'Aura', 'duration' => 233, 'date' => '2019-01-24', 'type' => 'single'],
            
            // SINGLE: PLACE ON FIRE
            ['name' => 'Place On Fire', 'duration' => 186, 'date' => '2019-02-07', 'type' => 'single'],
            
            // SINGLE: ATMOSPHERE
            ['name' => 'Atmosphere', 'duration' => 278, 'date' => '2019-04-18', 'type' => 'single'],
            
            // SINGLES REFLECTIONS
            ['name' => 'Reflections', 'duration' => 212, 'date' => '2019-07-25', 'type' => 'single'],
            
            // SINGLE: UNVEIL
            ['name' => 'Unveil', 'duration' => 87, 'date' => '2019-09-19', 'type' => 'single'],
            
            // SINGLE: RED HAZE
            ['name' => 'Red Haze', 'duration' => 200, 'date' => '2019-09-26', 'type' => 'single'],
            
            // SINGLE: NEVER MAKE IT
            ['name' => 'Never Make It', 'duration' => 234, 'date' => '2019-10-24', 'type' => 'single'],
            
            // SINGLE: SHINE
            ['name' => 'Shine', 'duration' => 198, 'date' => '2019-11-07', 'type' => 'single'],
            
            // EP: OCTANE
            ['name' => 'Crazy', 'duration' => 216, 'date' => '2020-01-16', 'type' => 'ep', 'coll' => 'Octane', 'order' => 1],
            ['name' => 'Ballistic Funk', 'duration' => 202, 'date' => '2020-01-16', 'type' => 'ep', 'coll' => 'Octane', 'order' => 2],
            ['name' => 'Octane', 'duration' => 216, 'date' => '2020-01-16', 'type' => 'ep', 'coll' => 'Octane', 'order' => 3],
            
            // SINGLE: LIGHTMARE
            ['name' => 'Lightmare', 'duration' => 302, 'date' => '2020-03-01', 'type' => 'single'],
            
            // SINGLE: DARK TIDES
            ['name' => 'Dark Tides', 'duration' => 212, 'date' => '2020-05-24', 'type' => 'single'],
            
            // SINGLE: ENDLESS
            ['name' => 'Endless', 'duration' => 201, 'date' => '2020-07-26', 'type' => 'single'],
            
            // SINGLE: EXOSPHERE
            ['name' => 'Exosphere', 'duration' => 205, 'date' => '2020-08-27', 'type' => 'single'],
            
            // SINGLE: AWAKEN
            ['name' => 'Awaken', 'duration' => 189, 'date' => '2020-10-01', 'type' => 'single'],
            
            // SINGLE: WORLDS
            ['name' => 'Worlds', 'duration' => 257, 'date' => '2020-12-21', 'type' => 'single'],

            // SINGLE: AURORA
            ['name' => 'Aurora', 'duration' => 201, 'date' => '2021-02-04', 'type' => 'single'],
            
            // SINGLE: WAVELIGHT
            ['name' => 'Wavelight', 'duration' => 223, 'date' => '2021-05-20', 'type' => 'single'],
            
            // SINGLE: DRIFT
            ['name' => 'Drift', 'duration' => 211, 'date' => '2021-06-24', 'type' => 'single'],
            
            // SINGLE: WE CAN DREAM
            ['name' => 'We Can Dream', 'duration' => 212, 'date' => '2021-12-09', 'type' => 'single'],
            
            // SINGLE: FLOW
            ['name' => 'Flow', 'duration' => 207, 'date' => '2022-05-19', 'type' => 'single'],
            
            // SINGLE: RED HORIZON
            ['name' => 'Red Horizon', 'duration' => 270, 'date' => '2022-07-07', 'type' => 'single'],
            
            // SINGLE: EVOLUTION
            ['name' => 'Evolution', 'duration' => 220, 'date' => '2022-12-22', 'type' => 'single'],
            
            // SINGLE: SKY AND SOUL
            ['name' => 'Sky and Soul', 'duration' => 254, 'date' => '2023-03-09', 'type' => 'single'],
            
            // SINGLE: COSMIC WAR
            ['name' => 'Cosmic War', 'duration' => 154, 'date' => '2023-04-30', 'type' => 'single'],
            
            // SINGLE: LOST
            ['name' => 'Lost', 'duration' => 186, 'date' => '2023-08-13', 'type' => 'single'],
            
            // SINGLE: IN SYNERGY
            ['name' => 'In Synergy', 'duration' => 181, 'date' => '2024-05-15', 'type' => 'single'],
            
            // SINGLE: DEEP DIVE
            ['name' => 'Deep Dive', 'duration' => 173, 'date' => '2024-08-01', 'type' => 'single'],
            
            // SINGLE: WITHOUT YOU
            ['name' => 'Without You', 'duration' => 175, 'date' => '2024-08-29', 'type' => 'single'],
            
            // SINGLE: MANTARAVE
            ['name' => 'Mantarave', 'duration' => 232, 'date' => '2024-09-26', 'type' => 'single'],
            
            // SINGLE: PROPHECY
            ['name' => 'Prophecy', 'duration' => 167, 'date' => '2025-05-01', 'type' => 'single'],
            
            // SINGLE: INTO THE NIGHT
            ['name' => 'Into the Night', 'duration' => 212, 'date' => '2025-05-01', 'type' => 'single'],
            
            // SINGLE: HEATSEEKER
            ['name' => 'Heatseeker', 'duration' => 206, 'date' => '2025-12-18', 'type' => 'single'],
            
            // SINGLE: RIPPLES TO WAVES
            ['name' => 'Ripples to Waves', 'duration' => 176, 'date' => '2025-12-18', 'type' => 'single'],
            
            // SINGLE: MARINE SNOW
            ['name' => 'Marine Snow', 'duration' => 295, 'date' => '2026-01-15', 'type' => 'single'],
            
            // SINGLE: RESTLESS
            ['name' => 'Restless', 'duration' => 228, 'date' => '2026-03-13', 'type' => 'single'],
            
            // SINGLE: FUTURE FANTASY
            ['name' => 'Future Fantasy', 'duration' => 261, 'date' => '2026-03-13', 'type' => 'single'],
            
        ];

        foreach ($discography as $item) {
            $slug = strtolower(str_replace(' ', '_', $item['name']));
            
            Song::create([
                'name' => $item['name'],
                'audio_path' => "songs/{$slug}.m4a", 
                'cover_path' => "covers/{$slug}.jpg",
                'duration' => $item['duration'],
                'release_date' => $item['date'],
                'type' => $item['type'],
                'collection_name' => $item['coll'] ?? null,
                'collection_order' => $item['order'] ?? null,
                'reproductions' => 0,
                'status' => 'active',
            ]);
        }
    }
}
