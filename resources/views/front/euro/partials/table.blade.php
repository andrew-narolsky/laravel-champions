{{-- $rows: rows from EuroStatsService; $showSeasons: bool; $logo: bool (default true); $mark: footnote closure (optional) --}}
<div class="table-scroll">
    <table class="alltime-table">
        <thead>
        <tr>
            <th>#</th>
            <th>Club</th>
            @if(!empty($showSeasons))<th title="Seasons">S</th>@endif
            <th title="Played">P</th>
            <th title="Won">W</th>
            <th title="Drawn">D</th>
            <th title="Lost">L</th>
            <th title="Goals">G</th>
            <th title="Goal difference">GD</th>
            <th title="Points">Pts</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rows as $row)
            <tr>
                <td class="rank">{{ $loop->iteration }}</td>
                <td class="club-name">@include('front.euro.partials.club', ['club' => $row['club'], 'logo' => $logo ?? true, 'year' => $year ?? null])</td>
                @if(!empty($showSeasons))<td>{{ $row['seasons'] }}</td>@endif
                <td>{{ $row['played'] }}</td>
                <td>{{ $row['won'] }}</td>
                <td>{{ $row['drawn'] }}</td>
                <td>{{ $row['lost'] }}</td>
                <td>{{ $row['goals_for'] }}:{{ $row['goals_against'] }}</td>
                <td>{{ $row['goal_difference'] > 0 ? '+' : '' }}{{ $row['goal_difference'] }}</td>
                <td class="titles">{{ $row['points'] }}@if(!empty($row['adjustment_note']) && isset($mark)){{ $mark($row['adjustment_note']) }}@endif</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
