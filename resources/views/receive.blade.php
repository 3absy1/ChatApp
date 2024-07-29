<div class="left message">
    @if(isset($attachment) && $attachment)
    {{-- <a href="{{ Storage::url($attachment) }}"alt="Attachment" >View</a> --}}
        <a href="{{ Storage::url($attachment) }}"><img src="{{ Storage::url($attachment) }}" alt="Attachment"></a>

    @endif
    <br>
    <p>{{$message}}</p>
</div>
