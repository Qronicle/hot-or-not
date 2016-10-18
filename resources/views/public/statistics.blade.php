@extends('layouts.default')



@section('content')
    <h2>Global statistics</h2>
    @if (count($subjects))
        <table>
            <colgroup>
                <col style="width: 15%">
                <col>
                <col style="width: 15%">
                <col style="width: 15%">
                <col style="width: 15%">
            </colgroup>
            <thead>
            <tr>
                <th>Pos.</th>
                <th>Name</th>
                <th>Wins</th>
                <th>Losses</th>
                <th>Win %</th>
            </tr>
            </thead>
            <tbody>
            @foreach($subjects as $i => $subject)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $subject->name }}</td>
                    <td>{{ $subject->times_won }}</td>
                    <td>{{ $subject->times_lost }}</td>
                    <td>{{ number_format($subject->percentage_won, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-danger">
            No statistics can be shown yet.
        </div>
    @endif

    @if ($lastMatch)
        <h2>Statistics for current match</h2>
        <table>
            <tbody>
            <tr>
                <th>Total matches</th>
                <td colspan="2">{{ $lastMatch->times_matched }}</td>
            </tr>
            <tr>
                <th>Amount of times {{ $lastMatch->subject1->name }} won</th>
                <td>{{ $lastMatch->subject1_wins }}</td>
                <td>({{ number_format( $lastMatch->subject1_wins * 100 / ($lastMatch->times_matched ?: 1), 2) }}%)</td>
            </tr>
            <tr>
                <th>Amount of times {{ $lastMatch->subject2->name }} won</th>
                <td>{{ $lastMatch->subject2_wins }}</td>
                <td>({{ number_format( $lastMatch->subject2_wins * 100 / ($lastMatch->times_matched ?: 1), 2) }}%)</td>
            </tr>
            </tbody>
        </table>
    @endif

@endsection


@section('content-class')content-medium
@endsection