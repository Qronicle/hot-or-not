@extends('layouts.default')

@section('content')
    @include('common.errors')
    @if ($subjects)
        <form action="/vote" method="post" class="face-off">
            @include('public.vote-subjects')
        </form>
    @else
        <div class="alert alert-danger">
            No subjects found
        </div>
    @endif
@endsection

@section('footer')
    @if ($subjects)
        <script>
            $(function () {
                var $form = $('form.face-off');
                var canChoose = true;
                var html = false;
                var isOut = false;
                $form.find('.incoming').removeClass('incoming');
                $form.on('click', '.subject-img', function (e) {
                    e.preventDefault();
                    // Prevent page from jumping to the top when next images are loading
                    $form.css('min-height', $form.height() + 'px');
                    // Prevent images from scaling automatically on small devices, because render error in Safari
                    //$form.find('.subject-img').each(function() {
                        $(this).css('height', $(this).height() + 'px');
                        //$(this).css('min-width', $(this).width() + 'px');
                    //});
                    if (!canChoose) return;
                    isOut = false;
                    canChoose = false;
                    $.ajax({
                        url: '/vote',
                        type: 'post',
                        data: {
                            key: $form.find('input[name=key]').val(),
                            subject: $(this).val()
                        },
                        headers: {
                            'X-CSRF-TOKEN': $form.find('input[name=_token]').val()
                        },
                        success: function (d) {
                            html = d;
                            startNewRound();
                        },
                        error: function () {
                            alert('Could not load the next match. Please refresh the page');
                        }
                    });
                    $(this).closest('.subject').addClass('the-chosen-one').siblings().addClass('the-deleted-one');
                    setTimeout(function () {
                        isOut = true;
                        startNewRound();
                    }, 1500);
                });

                function startNewRound() {
                    //@todo When html not set, display a loader or something
                    if (!html || !isOut) return;
                    $form.fadeOut(200, function () {
                        $form.html(html).show().find('.incoming').removeClass('incoming');
                        canChoose = true;
                    });
                }
            });
        </script>
    @endif
@endsection