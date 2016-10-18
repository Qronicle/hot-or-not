<input type="hidden" name="_token" value="{{ csrf_token() }}" />
<input type="hidden" name="key" value="{{$sessionKey}}" />
@foreach ($subjects as $subjectNr => $subject)
    <div class="subject subject-{{$subjectNr}}{{ $incoming ? ' incoming' : '' }}">
        <h2 class="subject-name">{{$subject->name}}</h2>
        <input class="subject-img subject-img-{{$subjectNr}}" name="subject" type="image" src="/img/subjects/{{$subject->id}}.jpg" value="{{$subject->id}}">
    </div>
@endforeach