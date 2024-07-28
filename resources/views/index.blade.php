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
{{--    <img src="/IMG_20220916_140627.jpg" alt="avatar" >--}}
    <div>
      <p>{{ $userName }}</p>
      <small>Online</small>

    </div>
  </div>
  <!-- End Header -->

  <!-- Chat -->
  <div class="messages">
    @include('receive', ['message' => "Hey! What's up!  👋"])
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
      <button type="submit"></button>
    </form>
  </div>
  <!-- End Footer -->

</div>
</body>

<script>
  const pusher  = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {cluster: 'mt1'});
Pusher.logToConsole = true;


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

</script>
</html>
