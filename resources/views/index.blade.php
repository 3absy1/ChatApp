<!DOCTYPE html>
<html lang="en">

<head>
    <title>Chat Laravel Pusher</title>
    {{--  <link rel="icon" href="https://assets.edlin.app/favicon/favicon.ico"/> --}}
    {{--  <meta name="viewport" content="width=device-width, initial-scale=1"> --}}

    <!-- JavaScript -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- End JavaScript -->

    <!-- CSS -->
    <link rel="stylesheet" href="/style.css">
    <style>
        .file-input-wrapper {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-wrapper svg {
            fill: currentColor;
            width: 32px;
            height: 32px;
        }
    </style>
    <!-- End CSS -->
</head>

<body>
    <div class="chat">
        <!-- Header -->
        <div class="top">
            <div>
                <a href="/" style="color: black; text-decoration: none;">&#9664;&nbsp; Back</a>
                <p>{{ $userName }}</p>
                @if ($userStatus == '0')
                    <small>Offline</small>
                @else
                    <small>Online</small>
                @endif
                <!-- Typing Indicator -->
                <div class="typing-indicator" style="display:none;">Typing...</div>
            </div>
        </div>

    </div>
    <!-- End Header -->
    <div id="chat" class="chat" style="height:500px;overflow: scroll">


        <!-- Chat -->
        <div class="messages">
            @include('receive', ['message' => "Hey! What's up!  👋"])
            @include('receive', [
                'message' => 'Ask a friend to open this link and you can chat with them!',
            ])

            @if (empty($sendMessages))
            @foreach ($allMessages as $message)
                @if ($message->sender == Auth::user()->id)
                    @include('broadcast', ['message' => $message->message, 'attachment' => $message->attachment])
                @else
                    @include('receive', ['message' => $message->message, 'attachment' => $message->attachment])
                @endif
            @endforeach
            @endif
        </div>
        <!-- End Chat -->



        <!-- Footer -->
        <div class="bottom">
            <form>
                <input type="text" id="message" name="message" placeholder="Enter message..." autocomplete="off">
                <div class="file-input-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                        <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z"/>
                    </svg>
                    <input type="file" id="file-input" name="attachment">
                </div>
                <button type="submit"></button>
            </form>
        </div>

        <!-- End Footer -->
    </div>
</body>

<script>
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: 'mt1'
    });
    Pusher.logToConsole = true;

    var chatChannel = pusher.subscribe('chat{{ Auth::user()->id }}');

 // Debounce function
 function debounce(func, delay) {
            let timeoutId;
            return function(...args) {
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        }

        // Typing event with debounce
        const sendTypingEvent = debounce(function() {
            $.post("{{ route('typing') }}", {
                _token: '{{ csrf_token() }}',
                user_id: '{{ $user_id }}'
            });
        }, 1000); // Adjust the delay as needed

        $('#message').on('input', sendTypingEvent);

        chatChannel.bind('user.typing', function(data) {
            if (data.userId !== '{{ Auth::user()->id }}') {
                $('.typing-indicator').show();
                setTimeout(function() {
                    $('.typing-indicator').hide();
                }, 3000);
            }
        });

    $("form").submit(function(event) {
        event.preventDefault();

        var formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('message', $("form #message").val());

        $.ajax({
            url: "/chat/{{ $user_id }}",
            method: 'POST',
            headers: {
                'X-Socket-Id': pusher.connection.socket_id
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $(".messages > .message").last().after(res);
                $("form #message").val('');
                $("form input[type='file']").val('');
                $(document).scrollTop($(document).height());
            }
        });
    });

    var channel = pusher.subscribe('chat{{ Auth::user()->id }}');
    channel.bind('chatMessage', function(data) {
        $.post("{{ route('receiveMessage') }}", {
            _token: '{{ csrf_token() }}',
            message: data.message,
            attachment: data.attachment
        }).done(function(res) {
            $(".messages").append(res);
            $(document).scrollTop($(document).height());
        });
    });

    let divElement = document.getElementById('chat');
    divElement.scrollTop = divElement.scrollHeight;
</script>

</html>
