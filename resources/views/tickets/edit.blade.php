@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Event</h2>
    <form id="editEventForm">
        @csrf
        @method('PUT')
        <input type="hidden" id="event_id" value="{{ $event->id }}">
        <div class="mb-3">
            <label for="title">Event Title</label>
            <input type="text" id="title" name="title" value="{{ $event->title }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" required>{{ $event->description }}</textarea>
        </div>
        <div class="mb-3">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" value="{{ $event->date }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" value="{{ $event->location }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tickets_available">Available Tickets</label>
            <input type="number" id="tickets_available" name="tickets_available" value="{{ $event->tickets_available }}" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Event</button>
       
    </form>
    <div id="message" class="mt-3"></div>
</div>

@endsection

@section('Scripts')
<script>
    $(document).ready(function () {
        $('#editEventForm').on('submit', function (e) {
            e.preventDefault();
            var eventId = $('#event_id').val();
            $.ajax({
                url: "{{ url('/events/') }}" + "/"+eventId,
                type: "PUT",
                data: $(this).serialize(),
                success: function (response) {
                    $('#message').html('<div class="alert alert-success">Event updated successfully!</div>');
                    window.location.href = "{{ url('/events') }}";
                },
                error: function (xhr) {
                    $('#message').html('<div class="alert alert-danger">Error updating event.</div>');
                }
            });
        });
    });
</script>
@endsection
