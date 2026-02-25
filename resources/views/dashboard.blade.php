<form method="POST" action="{{ route('collaboration.updateStatus', $collaboration->id) }}">
    @csrf
    @method('PATCH')

    <select name="status">
        <option value="idea" {{ $collaboration->status == 'idea' ? 'selected' : '' }}>Idea</option>
        <option value="contacted" {{ $collaboration->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
        <option value="replied" {{ $collaboration->status == 'replied' ? 'selected' : '' }}>Replied</option>
        <option value="negotiating" {{ $collaboration->status == 'negotiating' ? 'selected' : '' }}>Negotiating</option>
        <option value="active" {{ $collaboration->status == 'active' ? 'selected' : '' }}>Active</option>
        <option value="finished" {{ $collaboration->status == 'finished' ? 'selected' : '' }}>Finished</option>
        <option value="lost" {{ $collaboration->status == 'lost' ? 'selected' : '' }}>Lost</option>
    </select>

    <button type="submit">Update Status</button>
</form>
