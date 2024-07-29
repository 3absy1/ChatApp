<div class="right message">

    @if(isset($attachment) && $attachment)
        {{-- <img src="{{ Storage::url($attachment) }}" alt="Attachment"> --}}
        <a href="{{ Storage::url($attachment) }}"><img src="{{ Storage::url($attachment) }}" alt="Attachment"></a>

    @endif
    <br>
    <p>{!! $message !!}</p>
</div>
