<form action="{{ route('create-table') }}" method="POST">
    @csrf
    @foreach($columns as $column)
        <label>{{ $column }}: </label>
        <input type="text" name="columns[{{ $column }}]" value="{{ $column }}">
        <br>
@endforeach

    <input type="hidden" name="rows" value="{{ json_encode($rows) }}">

    <button type="submit">Create Table</button>
</form>
