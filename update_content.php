<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;
use App\Models\Chapter;

// Clear existing chapters to avoid duplicates
Chapter::truncate();

$book1 = Book::updateOrCreate(
    ['name' => 'The Book With No Name'],
    ['status' => 'in_progress']
);

$book2 = Book::updateOrCreate(
    ['name' => 'Peter Trull Solitary Detective'],
    ['status' => 'finished']
);

$noNameContent = <<<'EOD'
The heat was unbearable.
Canada had not seen temperatures like this in years. By noon, the thermometer outside the kitchen window had climbed past one hundred and ten degrees, and Ellen Hertz felt as if the air itself had turned thick and suffocating.
She felt like she was going crazy.
It was supposed to be a regular pregnancy. Nothing unusual. Why, then, were there so many emotional storms? Why did everything feel so overwhelming?
Maybe because it was noon. The temperature was unbearable. The power had gone out hours earlier.
No air conditioning. No fan.
And Bill still wasn’t home.
The first signs of labor had appeared half an hour earlier, no real pain, only a strange discomfort and the nagging certainty that the baby was coming.
Ellen sat on the couch, staring at the packed hospital bags beside the door.
Where are you, Bill?
The last time they had spoken, they had argued. Another pointless fight, made worse by exhaustion and pregnancy hormones. Now she wished she could take every word back.
Should she call a cab?
Maybe she should wait outside with the bags.
No. That would be crazy in this heat. She might faint before the cab even arrived.
Forty-five minutes had already passed.
Where are you, Bill? She thought again.
Why did she even want a baby at this stage in life?
She was only twenty-four.
Sometimes the whole responsibility felt far too big for her. But it was 1969, and everyone seemed to think this was what a young couple should want.
For whom? she wondered.
Not for me, for sure.
Then she heard tires screech outside.
Finally.
The heavy oak door opened slowly, and Bill stepped inside, completely drenched in sweat.
“Is everything alright?” he asked quickly. “Are there contractions? Did your water break?”
“Nothing like that,” Ellen said. “Just… a feeling. Like the baby is ready.”
Bill wiped his forehead.
“Sorry. The car overheated, and traffic was terrible. I had to drive all the way with no air conditioning.”
Ellen closed her eyes for a moment.
“I’m sorry for shouting,” she said quietly. “This childbirth thing is playing with my head.”
Bill smiled.
“I understand. It cannot be easy carrying all this extra weight in this heat. Especially with no air conditioning. Let’s grab the bags and head to the hospital.”
Ellen hesitated.
“Bill… I feel like the baby is waiting.”
“Waiting?”
“Yes,” she said quietly. “Like he knows something we don’t.”
Bill laughed softly.
“Maybe our child is special,” he said. “Maybe he’s waiting until we reach the hospital.”
He picked up the suitcase and headed toward the station wagon.
As Ellen followed him outside, a strange thought crossed her mind.
You are special, she thought toward the child inside her.
Something tells me you’re going to change everything.
Bill returned to help her into the car.
The drive to the hospital took almost an hour. Strangely, Ellen felt almost no pain during the ride.
Could this be a false alarm? she wondered. Maybe all this trouble is for nothing. Maybe you’re just playing with me, baby.
Just as the thought crossed her mind, a sharp discomfort moved through her belly.
Ellen smiled faintly.
Probably not a false alarm.
Bill pulled up in front of Green Pines Hospital and jumped out of the car.
“My wife is having a baby!” he shouted toward the entrance.
A nurse stepped outside and approached the car.
“How do you feel, Miss?”
“Surprisingly fine,” Ellen answered.
The nurse looked puzzled.
“No pains? No pressure?”
“Some discomfort,” Ellen admitted apologetically. “But I know I’m having a baby today. Could you please help me?”
The nurse nodded and disappeared inside.
A few minutes later, he returned with a wheelchair, accompanied by Dr. Oppenheim.
“Mrs. Hertz,” the doctor said calmly, “the nurse tells me you feel no real pain or pressure. Is that correct?”
“Yes, Doctor. But earlier, I felt something very clearly. The baby was ready to come out.”
The doctor’s face showed a trace of concern.
“Well, since you’re here, let’s go inside and have a look.”
As the nurse pushed Ellen down the hallway, the doctor turned quietly to Bill.
“Most likely this is a false alarm,” he said. “Childbirth usually involves long labor pains and contractions.”
“I understand,” Bill replied. “But if my wife says this is the moment, then it is. She hasn’t been wrong yet.”
The doctor smiled politely.
“There is almost no possibility that she will give birth right now. Her water hasn’t even broken.”
“If that’s true,” Bill said calmly, “this will be the first time she was wrong.”
Once Bill entered the maternity ward, dressed in hospital clothing, he found Ellen already lying on the delivery bed.
“Bill,” she whispered, “I feel this is the right time. I’m sure of it.”
“I’m with you all the way,” he said with a smile. “I carried those heavy bags for a reason.”
Dr. Oppenheim entered the room.
“You’re not dressed for delivery,” Bill said.
“I’m here to prove you wrong,” the doctor replied calmly.
“Please place your legs in the rests.”
For a moment, nothing happened.
Then Ellen screamed.
A sharp pain tore through her body. “What’s happening?” Bill shouted.
The doctor’s face turned pale. “Nurse! Prepare the delivery kit immediately!”
Three minutes later, he returned, fully prepared.
“I apologize,” he said breathlessly. “It appears you were both right.”
A few minutes later, I entered the world.
My name is Mike.
And I controlled my own birth.
I didn’t understand it then. In fact, it would take years before I realized what I had done.
The doctors, however, immediately understood that something unusual had happened. My mother felt no pain after I was born. No cut was made to her body, and she didn’t need any medication. Every test they ran on me came back perfect.
Too perfect.
Dr. Oppenheim asked my mother for permission to write about my delivery in a medical journal.
She refused.
At the time, everyone believed that decision was hers.
It wasn’t.
I made that decision for her.
Of course, I didn’t know that then either.
I was a healthy, chubby, blond boy, weighing exactly eight pounds at birth. And I loved my parents from the moment I felt them. Bill and Ellen Hertz.
Two good people who only wanted to make my life the best experience it could possibly be for a child. They did everything they could for me. Although, if I’m honest, I helped them behave that way.
At the time, I didn’t see anything strange about it.
After all, I was just a baby.
Mike
Knowledge is power.
He who controls knowledge
has the power.
But what about those
who can control knowledge—
control others
and take their power away?
EOD;

$peterTrullA = <<<'EOD'
I woke up tired. Another night lost to struggle. My head throbbed, my jaw ached, and something tight tugged at the muscles in my neck. The room was dark, but dawn was bleeding through the edges of the window—muted light on heavy curtains.
It was early. It was always early. The silence outside felt padded, like snow had fallen, though none had. I woke at the same hour every day. Routine was armor. Brush teeth. Make breakfast. Take meds. Survive. One step at a time—I repeated it like a prayer to no one. One step at a time.
Cat—he only responded to that name—lay curled at my feet. His ears flicked when I stirred. I’d have to feed him soon.
I rose slowly. The headache pressed like a hand on my skull, but still, I moved. Staying in bed was not an option. Stillness was a trap. Inaction invited something worse than pain.
In the bathroom, the mirror gave me back a tired face—deep lines carved beneath my eyes, aging me beyond my years. I washed my face, hoping cold water would ease the pressure. My body was in decent shape—I trained at the base—but the damage from years of obesity clung like an old scar, invisible but ever present.
Two weeks. That’s how long it had been since everything shifted. Since it all slipped back to what it was before. I’d had a few extraordinary months—months where the pain retreated, where I almost felt alive. I would trade anything to go back. But now she was gone, and I was here, just another shadow in my own apartment, waiting for a ghost to reappear.
The electric toothbrush buzzed to life—two minutes and thirty seconds per quadrant. I complied like I always did.
Then the shower. Hot water beat down on the back of my neck, easing the tension. By the time I dried off, the pain had dulled enough for me to function. I dressed, fed Cat, poured him water, and stepped out for coffee.
My apartment was small—one bed, one bath. A shoebox tucked off a narrow alley near 51st Street. It was enough—a wall of books, a beat-up loveseat, and a recliner from IKEA. The kitchen table sat by the window. I liked it there. I could watch the street, see who came and went. It made eating alone a little less lonely.
Most mornings, I went to the coffee shop just a few doors down.
The street was empty as I descended the stairs. I scanned the sidewalk. Quiet. Still. No movement. I felt safe enough to leave.
Inside the café, the air was warm with the scent of roasted beans and the sound of stale chatter. A few early risers stood in line. I took my usual booth by the window and waited for Ellaine. She was already watching me, red dress beneath the apron. Probably had a date later. I offered a polite smile, trying to bury the anxiety curling in my chest.
I ordered my usual—black coffee, strong, no cream, and a ball of fruit and nuts.
She brought my coffee with her usual grace, and I thanked her with a nod. While I waited for the food, I watched the others.
Seven people in line—three men, four women. I scanned each one the way I always did.
First guy—tech type. Slack posture, bored eyes, start-up hoodie. Low risk.
Next—a construction worker. Wrinkled shirt, dust on his boots. Looked like he’d been up since four. Exhausted. Envious of anyone seated. No threat.
Then a woman, phone glued to her ear, mascara smudged. It looked like last night’s dress, a bad date aftermath—low risk.
Another woman behind her, pretending not to listen. Subtle shifts, leaned in when the phone call got heated. Office attire, leather bag—probably a lawyer. Low risk.
The rest are similar—all ordinary.
Except for the couple behind me.
I couldn’t see them, just heard murmurs. The woman’s voice was tight. The man’s was too calm. Something in that calm raised the hairs on my arms.
I considered getting up to look, but stayed seated. Instead, I shifted my position, subtly angling toward the window to catch a glimpse from the corner of my eye.
They looked normal. Jeans. Windbreakers. Tourists maybe. But something was off. His hand never left the table. Her fingers tapped, stilled, tapped again. My pulse quickened. I didn’t know why.
When my food arrived, I turned back to face the front. My senses stayed on alert, trying to catch snatches of their voices.
“We need to do as we were told,” the man said.
Then silence.
They stood. Left their table. I watched the man’s gait—too eager, almost springy. The woman lagged behind. He grabbed her arm. She resisted slightly. He jerked it. She followed.
Just before they turned the corner, she looked back.
Right at me.
And then she disappeared.
That’s when I saw the briefcase.
It sat alone, tucked beneath their table.
When Ellaine brought my check, I told her.
“Oh,” she said, too lightly. “Thanks.”
She picked it up and took it behind the counter.
I paid, left a ten-dollar tip, and slid out of the booth.
The air outside was cool against my skin. I stepped onto the sidewalk—
And froze.
Every hair on my body stood upright.
I turned to look back—
But I never got the chance.
The coffee shop exploded.
EOD;

$peterTrullB = <<<'EOD'
I woke up tired.
Another night lost to struggle. My jaw ached; my head throbbed; something pulled tight at the base of my neck. Dawn bled through the curtains in a thin gray seam.
Early. Always early.
The silence outside felt padded, as if snow had fallen. It hadn’t.
Routine was armor. Brush teeth. Make breakfast. Take meds. Survive.
One step at a time.
Cat—he only responded to that name—lay curled at my feet. His ears flicked when I stirred. I’d have to feed him soon.
I rose slowly. The headache pressed like a hand against my skull, but staying in bed was not an option. Stillness was a trap.
In the bathroom, the mirror gave me back a tired face. Deep lines beneath my eyes, aging me beyond my years. I splashed cold water, hoping to dull the pressure.
My body was in decent shape—I trained at the base—but years of obesity lingered like a scar. Invisible. Persistent.
Two weeks. That was how long it had been since everything shifted. Since it slipped back to what it had always been.
I’d had a few extraordinary months. Months when the pain retreated. When I almost felt alive.
I would trade anything to go back.
Now she was gone, and I was here—another shadow in my own apartment.
The electric toothbrush buzzed to life. Two minutes and thirty seconds per quadrant. I complied.
Then the shower. Hot water beat against the back of my neck, easing the tension. By the time I dried off, the pain had dulled enough for me to function.
I dressed, fed Cat, poured him water, and stepped out for coffee.
My apartment was small—one bedroom, one bath. A shoebox tucked off a narrow alley near Fifty-First Street. A wall of books. A worn loveseat. A recliner from IKEA. The kitchen table sat by the window. I liked it there. I could watch the street—see who came and went.
It made eating alone feel less lonely.
Most mornings, I went to the coffee shop a few doors down.
The street was empty as I descended the stairs. I scanned the sidewalk. Quiet. Still.
Safe enough.
Inside the café, warmth and the scent of roasted beans. A few early risers stood in line. I took my usual booth by the window and waited for Ellaine. She was already watching me, red dress beneath her apron.
I offered a polite smile.
“Black coffee,” I said. “Strong. No cream. And a fruit-and-nut ball.”
Seven people in line—three men, four women. I scanned them the way I always did.
Tech type. Slack posture. Start-up hoodie.
Construction worker. Dust on his boots. Exhausted.
Woman on her phone. Mascara smudged.
Another leaning in to listen. Office attire. Leather bag.
Ordinary.
Except for the couple behind me.
I couldn’t see them at first—only hear them.
Her voice was tight. His was too calm.
I shifted in my seat, angling toward the window to catch their reflection.
Jeans. Windbreakers.
His hand never left the table. Her fingers tapped once, then stilled.
My pulse quickened.
When my food arrived, I turned forward again but kept listening.
“We need to do as we were told,” the man said.
Then silence.
They stood and left their table.
I watched the man’s gait—too eager, almost springy. The woman lagged behind. He grabbed her arm. She resisted slightly. He jerked it. She followed.
Just before they turned the corner, she looked back.
Right at me.
Then she disappeared.
That’s when I saw the briefcase.
It sat alone beneath their table.
When Ellaine brought my check, I told her.
“Oh,” she said lightly. “Thanks.”
She picked it up and carried it behind the counter.
I paid, left a ten-dollar tip, and stepped outside.
Cool air touched my face.
I froze.
Every hair on my body stood upright.
I turned to look back—
But I never got the chance.
The coffee shop exploded.
EOD;

Chapter::create([
    'book_id' => $book1->id,
    'title' => 'The Day I Was Born',
    'number' => 1,
    'content' => $noNameContent,
    'version' => 'A',
    'status' => 'published',
]);

Chapter::create([
    'book_id' => $book2->id,
    'title' => 'Beginning of the end',
    'number' => 1,
    'content' => $peterTrullA,
    'version' => 'A',
    'status' => 'published',
]);

Chapter::create([
    'book_id' => $book2->id,
    'title' => 'Beginning of the end',
    'number' => 1,
    'content' => $peterTrullB,
    'version' => 'B',
    'status' => 'published',
]);

echo "Database updated successfully.\n";
