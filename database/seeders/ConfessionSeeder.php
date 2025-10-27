<?php

namespace Database\Seeders;

use App\Models\Confession;
use Illuminate\Database\Seeder;

class ConfessionSeeder extends Seeder
{
    public function run(): void
    {
        Confession::create([
            'name' => [
                'uk' => 'Українська православна церква Київського патріархату',
                'en' => 'Ukrainian Orthodox Church of the Kyiv Patriarchate',
                'de' => 'Ukrainische Orthodoxe Kirche des Kiewer Patriarchats',
                'ro' => 'Biserica Ortodoxă Ucraineană a Patriarhatului de Kiev',
                'ka' => 'უკრაინის მართლმადიდებელი ეკლესია კიევის პატრიარქატის'
            ],
            'full_name' => [
                'uk' => 'Українська православна церква Київського патріархату',
                'en' => 'Ukrainian Orthodox Church of the Kyiv Patriarchate',
                'de' => 'Ukrainische Orthodoxe Kirche des Kiewer Patriarchats',
                'ro' => 'Biserica Ortodoxă Ucraineană a Patriarhatului de Kiev',
                'ka' => 'უკრაინის მართლმადიდებელი ეკლესია კიევის პატრიარქატის'
            ],
            'description' => [
                'uk' => 'Православна церква в Україні',
                'en' => 'Orthodox Church in Ukraine',
                'de' => 'Orthodoxe Kirche in der Ukraine',
                'ro' => 'Biserica Ortodoxă din Ucraina',
                'ka' => 'მართლმადიდებელი ეკლესია უკრაინაში'
            ],
            'emoji' => '☦️',
            'country_ids' => [1],
            'active' => true,
        ]);

        Confession::create([
            'name' => [
                'uk' => 'Свідки Єгови',
                'en' => 'Jehovah\'s Witnesses',
                'de' => 'Zeugen Jehovas',
                'ro' => 'Martorii lui Iehova',
                'ka' => 'იეჰოვას მოწმეები'
            ],
            'full_name' => [
                'uk' => 'Свідки Єгови',
                'en' => 'Jehovah\'s Witnesses',
                'de' => 'Zeugen Jehovas',
                'ro' => 'Martorii lui Iehova',
                'ka' => 'იეჰოვას მოწმეები'
            ],
            'description' => [
                'uk' => 'Християнська релігійна організація',
                'en' => 'Christian religious organization',
                'de' => 'Christliche Religionsgemeinschaft',
                'ro' => 'Organizație religioasă creștină',
                'ka' => 'ქრისტიანული რელიგიური ორგანიზაცია'
            ],
            'emoji' => '📖',
            'country_ids' => [1],
            'active' => true,
        ]);

        Confession::create([
            'name' => [
                'uk' => 'Українська греко-католицька церква',
                'en' => 'Ukrainian Greek Catholic Church',
                'de' => 'Ukrainische griechisch-katholische Kirche',
                'ro' => 'Biserica Greco-Catolică Ucraineană',
                'ka' => 'უკრაინის ბერძნულ-კათოლიკური ეკლესია'
            ],
            'full_name' => [
                'uk' => 'Українська греко-католицька церква',
                'en' => 'Ukrainian Greek Catholic Church',
                'de' => 'Ukrainische griechisch-katholische Kirche',
                'ro' => 'Biserica Greco-Catolică Ucraineană',
                'ka' => 'უკრაინის ბერძნულ-კათოლიკური ეკლესია'
            ],
            'description' => [
                'uk' => 'Східна католицька церква',
                'en' => 'Eastern Catholic Church',
                'de' => 'Ostkatholische Kirche',
                'ro' => 'Biserica Catolică Orientală',
                'ka' => 'აღმოსავლეთ კათოლიკური ეკლესია'
            ],
            'emoji' => '✝️',
            'country_ids' => [1],
            'active' => true,
        ]);
    }
}