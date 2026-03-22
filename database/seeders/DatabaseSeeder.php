<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // You should change this after first login
                'is_admin' => true,
                'points' => 0,
            ]
        );

        // 2. Create Books
        $book1 = Book::updateOrCreate(
            ['id' => 1],
            ['name' => 'The Book With No Name', 'status' => 'in_progress']
        );

        $book2 = Book::updateOrCreate(
            ['id' => 2],
            ['name' => 'Peter Trull Solitary Detective', 'status' => 'in_progress']
        );

        // 3. Create Initial Chapters
        // Chapter 1 for Book 1
        Chapter::updateOrCreate(
            ['id' => 1],
            [
                'book_id' => 1,
                'number' => 1,
                'title' => 'The Beginning',
                'content' => "He was born on a freezing night in Yugoslavia.\nHis mother did not know who his father was. By the time the pain began, it was already too late to reach St. Catherine’s Church. The contractions took her in a narrow side street beside a shuttered bar, deep in an alley where the cold clung to the walls.\nThe pain did not come in waves. It held her and tightened.\nSomething inside her was tearing open. She felt the child forcing his way out of her, relentless, indifferent to her pleas. She whispered for him to wait. Just a little longer. Just enough time to reach the light.\nThe answer was another surge, sharper, final.\nShe understood then that she would die there.\nWarm blood spread down her legs, steaming in the cold air. Her scream broke against the walls and died there, swallowed by the alley. No one came.\nFor a moment, there was only the sound of her breath failing.\nThen footsteps.\nA man appeared at the mouth of the alley, his shape cut from shadow. He hesitated before stepping closer, as though something about the scene resisted him.\n“Are you all right?”\nShe tried to answer, but the pain sealed her voice. Her vision fractured. The world narrowed to fragments. She wondered why she had not yet lost consciousness. Why would the dark not take her?\nThe man spoke again, louder now. He heard him calling for help, his voice distant, already slipping away from her.\nHands would come. They would lift her. They would carry her into the light.\nShe would never feel it.\nHer last thought was simple, fragile.\nThe child would live differently. He would not carry what she had carried. He would not become what she had become.\nBut beneath that hope, something colder stirred.\nA certainty.\nHe would not escape.\nPain. Blood. Death. It had already begun.\nShe died as he entered the world.\nNo one held him when he first cried.\nPete came into life alone.\nAlone, and already claimed.\nThere were forces that had marked him long before breath filled his lungs. They had waited through bloodlines, through silence, through years that no one remembered. He was not chosen in any way that allowed refusal. He was taken.\nHe was the other.\nAnd the other did not belong to himself.\nHis life would narrow to a single point. A target set before he could see, before he could understand. Whether he resisted or obeyed would not matter in the end. The path would close around him, step by step, until nothing remained but its purpose.\nIf compassion ever found him, it would not survive him.\nOne side would claim what it had been promised.\nThe other would not forgive failure.\nHe would learn this.\nHe would live it.\nHe never knew his mother or father.\nAfter a few days in the hospital, he was sent to an orphanage in the barren farmlands of Yugoslavia.\nNo one there knew what had been born.",
                'version' => 'A',
                'status' => 'published',
                'is_locked' => true,
            ]
        );

        // Chapter 2 for Book 1
        Chapter::updateOrCreate(
            ['id' => 4],
            [
                'book_id' => 1,
                'number' => 2,
                'title' => 'First of the others',
                'content' => "He was born on a freezing night in Yugoslavia.\nHis mother did not know who his father was. By the time the pain began, it was already too late to reach St. Catherine’s Church. The contractions took her in a narrow side street beside a shuttered bar, deep in an alley where the cold clung to the walls.\nThe pain did not come in waves. It held her and tightened.\nSomething inside her was tearing open. She felt the child forcing his way out of her, relentless, indifferent to her pleas. She whispered for him to wait. Just a little longer. Just enough time to reach the light.\nThe answer was another surge, sharper, final.\nShe understood then that she would die there.\nWarm blood spread down her legs, steaming in the cold air. Her scream broke against the walls and died there, swallowed by the alley. No one came.\nFor a moment, there was only the sound of her breath failing.\nThen footsteps.\nA man appeared at the mouth of the alley, his shape cut from shadow. He hesitated before stepping closer, as though something about the scene resisted him.\n“Are you all right?”\nShe tried to answer, but the pain sealed her voice. Her vision fractured. The world narrowed to fragments. She wondered why she had not yet lost consciousness. Why would the dark not take her?\nThe man spoke again, louder now. He heard him calling for help, his voice distant, already slipping away from her.\nHands would come. They would lift her. They would carry her into the light.\nShe would never feel it.\nHer last thought was simple, fragile.\nThe child would live differently. He would not carry what she had carried. He would not become what she had become.\nBut beneath that hope, something colder stirred.\nA certainty.\nHe would not escape.\nPain. Blood. Death. It had already begun.\nShe died as he entered the world.\nNo one held him when he first cried.\nPete came into life alone.\nAlone, and already claimed.\nThere were forces that had marked him long before breath filled his lungs. They had waited through bloodlines, through silence, through years that no one remembered. He was not chosen in any way that allowed refusal. He was taken.\nHe was the other.\nAnd the other did not belong to himself.\nHis life would narrow to a single point. A target set before he could see, before he could understand. Whether he resisted or obeyed would not matter in the end. The path would close around him, step by step, until nothing remained but its purpose.\nIf compassion ever found him, it would not survive him.\nOne side would claim what it had been promised.\nThe other would not forgive failure.\nHe would learn this.\nHe would live it.\nHe never knew his mother or father.\nAfter a few days in the hospital, he was sent to an orphanage in the barren farmlands of Yugoslavia.\nNo one there knew what had been born.",
                'version' => 'A',
                'status' => 'published',
                'is_locked' => false,
            ]
        );

        // Chapter 1 for Book 2 (Peter Trull)
        Chapter::updateOrCreate(
            ['id' => 2],
            [
                'book_id' => 2,
                'number' => 1,
                'title' => 'Peter Trull - Case 1',
                'content' => "Peter Trull sat in his office, the neon sign outside flickering like a dying heartbeat. He was a solitary detective in a city that never slept, but always dreamed of nightmares.",
                'version' => 'A',
                'status' => 'published',
                'is_locked' => false,
            ]
        );
    }
}
