<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuoteController extends Controller
{
    private $quotes = [
        ['quote' => 'The biggest battle is the war against ignorance.', 'author' => 'Mustafa Kemal Atatürk'],
        ['quote' => 'Education is the most powerful weapon which you can use to change the world.', 'author' => 'Nelson Mandela'],
        ['quote' => 'The only way to do great work is to love what you do.', 'author' => 'Steve Jobs'],
        ['quote' => 'Learning never exhausts the mind.', 'author' => 'Leonardo da Vinci'],
        ['quote' => 'The beautiful thing about learning is that no one can take it away from you.', 'author' => 'B.B. King'],
        ['quote' => 'Live as if you were to die tomorrow. Learn as if you were to live forever.', 'author' => 'Mahatma Gandhi'],
        ['quote' => 'The mind is not a vessel to be filled, but a fire to be kindled.', 'author' => 'Plutarch'],
        ['quote' => 'An investment in knowledge pays the best interest.', 'author' => 'Benjamin Franklin'],
        ['quote' => 'The more I read, the more I acquire, the more certain I am that I know nothing.', 'author' => 'Voltaire'],
        ['quote' => 'Tell me and I forget. Teach me and I remember. Involve me and I learn.', 'author' => 'Benjamin Franklin'],
        ['quote' => 'The expert in anything was once a beginner.', 'author' => 'Helen Hayes'],
        ['quote' => 'Failure is the opportunity to begin again more intelligently.', 'author' => 'Henry Ford'],
        ['quote' => 'The only source of knowledge is experience.', 'author' => 'Albert Einstein'],
        ['quote' => 'Knowledge is power.', 'author' => 'Francis Bacon'],
        ['quote' => 'The capacity to learn is a gift; the ability to learn is a skill; the willingness to learn is a choice.', 'author' => 'Brian Herbert'],
        ['quote' => 'The best way to predict the future is to create it.', 'author' => 'Peter Drucker'],
        ['quote' => 'It is not that I am so smart. But I stay with the questions much longer.', 'author' => 'Albert Einstein'],
        ['quote' => 'The illiterate of the 21st century will not be those who cannot read and write, but those who cannot learn, unlearn, and relearn.', 'author' => 'Alvin Toffler'],
        ['quote' => 'Every act of conscious learning requires the willingness to suffer an injury to one\'s self-esteem. That is why young children, before they are aware of their own self-importance, learn so easily.', 'author' => 'Thomas Szasz'],
        ['quote' => 'We learn more by looking for the answer to a question and not finding it than we do from learning the answer itself.', 'author' => 'Lloyd Alexander'],
        ['quote' => 'You don\'t learn to walk by following rules. You learn by doing, and by falling over.', 'author' => 'Richard Branson'],
        ['quote' => 'The only person who is educated is the one who has learned how to learn and change.', 'author' => 'Carl Rogers'],
        ['quote' => 'Learning is a treasure that will follow its owner everywhere.', 'author' => 'Chinese Proverb'],
        ['quote' => 'The more that you read, the more things you will know. The more that you learn, the more places you\'ll go.', 'author' => 'Dr. Seuss'],
        ['quote' => 'Education is not preparation for life; education is life itself.', 'author' => 'John Dewey'],
        ['quote' => 'Develop a passion for learning. If you do, you will never cease to grow.', 'author' => 'Anthony J. D\'Angelo'],
        ['quote' => 'The secret of getting ahead is getting started.', 'author' => 'Mark Twain'],
        ['quote' => 'It does not matter how slowly you go as long as you do not stop.', 'author' => 'Confucius'],
        ['quote' => 'Success is the sum of small efforts, repeated day in and day out.', 'author' => 'Robert Collier'],
        ['quote' => 'Believe you can and you\'re halfway there.', 'author' => 'Theodore Roosevelt'],
        ['quote' => 'Your time is limited, don\'t waste it living someone else\'s life.', 'author' => 'Steve Jobs'],
    ];

    public function random()
    {
        $quote = $this->quotes[array_rand($this->quotes)];
        return response()->json($quote);
    }
}
