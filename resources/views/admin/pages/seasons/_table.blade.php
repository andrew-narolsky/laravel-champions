@if($competition->seasons->isEmpty())
    <span>No seasons found for this competition.</span>
@else
    <table class="table mb-0 table-striped">
        <thead>
        <tr>
            <th style="width: 50px">#</th>
            <th style="width: 200px;">Competition</th>
            <th>Season</th>
            <th style="width: 200px; text-align: right">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($competition->seasons as $season)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $competition->name }}</td>
                <td>{{ $season->name }}</td>
                <td class="d-flex justify-content-end">
                    <a href="{{ route('competitions.seasons.edit', [$competition, $season]) }}" class="btn btn-inverse-info btn-icon me-1">
                        <i class="mdi mdi-pencil"></i>
                    </a>
                    <form method="POST" action="{{ route('competitions.seasons.destroy', [$competition, $season]) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-inverse-danger btn-icon">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
