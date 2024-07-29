<!DOCTYPE html>
<html lang="en">
<head>
  <title>Chat Laravel Pusher</title>
{{--  <link rel="icon" href="https://assets.edlin.app/favicon/favicon.ico"/>--}}
{{--  <meta name="viewport" content="width=device-width, initial-scale=1">--}}

  <!-- JavaScript -->
  <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

  <!-- End JavaScript -->

  <!-- CSS -->
  <link rel="stylesheet" href="/style.css">
  <!-- End CSS -->
</head>

<body>
    <div class="chat">
        <!-- Header -->
        <div class="top">
            <div>
                <a href="/" style="color: black; text-decoration: none;">&#9664;&nbsp; Back</a>
                <p>{{ $userName }}</p>
                @if ($userStatus == "0")
                <small >Offline</small>

                @else
                <small >Online</small>
                @endif
                        <!-- Typing Indicator -->
        <div class="typing-indicator" style="display:none;">Typing...</div>
            </div>
        </div>

        </div>
        <!-- End Header -->
    <div id="chat" class="chat" style="height:320px;overflow: scroll">


        <!-- Chat -->
        <div class="messages">
            @include('receive', ['message' => "Hey! What's up!  👋"])
            @include('receive', ['message' => "Ask a friend to open this link and you can chat with them!"])

            @if (empty($sendMessages))
                @foreach ($allMessages as $message)
                    @if ($message->sender == Auth::user()->id)
                        @include('broadcast', ['message' => $message->message])
                    @else
                        @include('receive', ['message' => $message->message])
                    @endif
                @endforeach
            @endif
        </div>
        <!-- End Chat -->



        <!-- Footer -->
        <div class="bottom">
            <form>
                <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
                <div id="submit-button-container">
                    <button type="submit"></button>
                </div>
            </form>
        </div>
        <!-- End Footer -->
    </div>
</body>

<script>
    const pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', { cluster: 'mt1' });
    Pusher.logToConsole = true;

    var chatChannel = pusher.subscribe('chat{{ Auth::user()->id }}');

// Typing event
$('#message').on('keypress', function () {
    $.post("{{ route('typing') }}", {
        _token: '{{ csrf_token() }}',
        user_id: '{{ $user_id }}'
    });
});

chatChannel.bind('user.typing', function (data) {
    if (data.userId !== '{{ Auth::user()->id }}') {
        $('.typing-indicator').show();
        setTimeout(function () {
            $('.typing-indicator').hide();
        }, 3000);
    }
});

        //Broadcast messages
        $("form").submit(function (event) {
            event.preventDefault();

            $.ajax({
            url:     "/chat/{{$user_id}}",
            method:  'POST',
            headers: {
                'X-Socket-Id': pusher.connection.socket_id
            },
            data:    {
                _token:  '{{csrf_token()}}',
                message: $("form #message").val(),
            }
            }).done(function (res) {
            $(".messages > .message").last().after(res);
            $("form #message").val('');
            $(document).scrollTop($(document).height());
            });
        });


            var channel = pusher.subscribe('chat{{Auth::user()->id}}');
            channel.bind('chatMessage', function(data) {
                $.post("{{ route('receiveMessage') }}", {
            _token: '{{ csrf_token() }}',
            message: data.message
            }).done(function(res) {
            $(".messages").append(res);
            $(document).scrollTop($(document).height());
            });
            });

            let divElement = document.getElementById('chat');
            divElement.scrollTop = divElement.scrollHeight;


    </script>
        </html>
