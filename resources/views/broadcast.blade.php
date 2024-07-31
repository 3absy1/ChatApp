{{-- <div class="right message">

    @if(isset($attachment) && $attachment)
        <img src="{{ Storage::url($attachment) }}" alt="Attachment">
        <a href="{{ Storage::url($attachment) }}"><img src="{{ Storage::url($attachment) }}" alt="Attachment"></a>

    @endif
    <br>
    <p>{!! $message !!}</p>
</div> --}}

<div class="right message">
    @if(isset($attachment) && $attachment)
        @php
            $attachmentUrl = Storage::url($attachment);
            $attachmentType = pathinfo($attachment, PATHINFO_EXTENSION);
        @endphp

        @if(in_array($attachmentType, ['jpg', 'jpeg', 'png', 'gif']))
            <a href="{{ $attachmentUrl }}" target="_blank">
                <img src="{{ $attachmentUrl }}" alt="Attachment" style="max-width:80%; height: 100px;">
            </a>
        @elseif($attachmentType === 'pdf')
            <a href="{{ $attachmentUrl }}" target="_blank">{{ basename($attachment) }}</a>
        @else
            <a href="{{ $attachmentUrl }}" target="_blank">{{ basename($attachment) }}</a>
        @endif
    @endif
    <br>
    <p>{!! $message !!}</p>
</div>
