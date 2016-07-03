<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use LaravelPusher;

class MessagesController extends Controller
{

    protected $pusher;

    function __construct()
    {
        $this->middleware('jwt.auth');
        $this->currentUser = JWTAuth::toUser(JWTAuth::getToken());
    }

    /**
     * Show all of message threads to current user
     *
     * @return mixed
     */
    public function index()
    {
        $threads = [];
        $currentUserId = $this->currentUser->id;
        $threads = Thread::forUser($currentUserId)->get();

        if (count($threads)) {
            foreach ($threads as &$thread) {
                // get last message
                $thread['last_message'] = $thread->getLatestMessageAttribute();
                // get email of participant (not current user)
                $thread['partner'] = $thread->participantsString($currentUserId, ['email']);
                $thread['userUnreadMessagesCount'] = $thread->userUnreadMessagesCount($currentUserId);
            }
        }

        return Response::json(compact('threads', 'currentUserId'), 200);
    }

    /**
     * show specify thread
     * @param  $id
     * @return mixed
     */
    public function show($id)
    {
        $currentUserId = $this->currentUser->id;

        try {
            $thread = Thread::with(['participants' => function($query) use ($currentUserId){
                $query->where('user_id', '<>', $currentUserId);
            }, 'messages'])->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Response::json(['error' => 'The thread with ID: ' . $id . ' was not found.'], 404);
        }
        $partnerId = $thread['participants'][0]['user_id'];
        $users = User::whereNotIn('id', $thread->participantsUserIds($currentUserId))->get();
        $thread->markAsRead($currentUserId);
        return Response::json(compact('thread', 'partnerId', 'currentUserId'), 200);
    }

    /**
     * store new message thread
     * @return mixed
     */
    public function store()
    {
        $input = Input::all();
        $thread = Thread::create(
            [
                'subject' => $input['subject'],
            ]
        );
        // Sender
        Participant::create(
            [
                'thread_id' => $thread->id,
                'user_id'   => $this->currentUser->id,
                'last_read' => new Carbon,
            ]
        );
        // Recipients
        if (Input::has('recipients')) {
            // recipients: list of recipients' user_ids
            $thread->addParticipants($input['recipients']);
        }
        // Message
        if (Input::has('message')) {
            $message = Message::create([
                'thread_id' => $thread->id,
                'user_id'   => $this->currentUser->id,
                'body'      => $input['message'],
            ]);
            $this->oooPushIt($message);
        }
        return Response::json(compact('thread'), 200);
    }

    /**
     * add a message to specify thread
     * @param  $id thread_id
     * @return mixed
     */
    public function update($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return Response::json(['error' => 'The thread with ID: ' . $id . ' was not found.'], 404);
        }
        $thread->activateAllParticipants();
        // Message
        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id'   => $this->currentUser->id,
            'body'      => Input::has('body') ? Input::get('body') : '',
        ]);
        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id'   => $this->currentUser->id,
        ]);
        $participant->last_read = new Carbon;
        $participant->save();
        // Recipients
        if (Input::has('recipients')) {
            $thread->addParticipants(Input::get('recipients'));
        }

        $this->oooPushIt($message);
        return Response::json(compact('thread'), 200);
    }

    /**
     * Send the new message to Pusher in order to notify users.
     *
     * @param Message $message
     */
    protected function oooPushIt(Message $message)
    {
        $thread = $message->thread;
        $sender = $message->user;
        // FIXME make me show in right way
        $data = [
            'thread_id' => $thread->id,
            'div_id' => 'thread_' . $thread->id,
            'sender_name' => $sender->email,
            'sender_id' => $sender->id,
            'thread_url' => '#/threads/' . $thread->id,
            'thread_subject' => $thread->subject,
            'thread_created_at' => $thread->created_at,
            'html' => '',
            'text' => str_limit($message->body, 50),
        ];
        $recipients = $thread->participantsUserIds();
        if (count($recipients) > 0) {
            foreach ($recipients as $recipient) {
                if ($recipient == $sender->id) {
                    continue;
                }
                Log::info($recipient);
                LaravelPusher::trigger('for_user_' . $recipient, 'new_message', $data);
                // LaravelPusher::trigger('test_channel', 'test_event', $data);
            }
        }
    }

    /**
     * get threads which user_id joined
     * @param  $id
     * @return mixed
     */
    public function threadsByParticipants($id)
    {
        $currentUserId = $this->currentUser->id;
        $threads = Thread::forUser($currentUserId)->whereHas('participants', function ($query) use ($id)
        {
            $query->where('user_id', $id);
        })->get();

        return Response::json(compact('threads'), 200);
    }
}
