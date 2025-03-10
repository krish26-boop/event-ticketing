@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Event</h2>
    <form id="eventForm" method="post">
        @csrf
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="mb-3">
            <label for="title">Event Title</label>
            <input type="text" id="title" name="title" class="form-control">
        </div>
        <div class="mb-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="date">Date</label>
            <input type="date" id="date" name="date" class="form-control">
        </div>
        <div class="mb-3">
            <label for="location">Location</label>
            <input type="text" id="location" name="location" class="form-control">
        </div>
        <div class="mb-3">
            <label for="tickets_available">Available Tickets</label>
            <input type="number" id="tickets_available" name="tickets_available" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Create Event</button>
    </form>
    <div id="message" class="mt-3"></div>
</div>
@endsection

@section('Scripts')
<script>
    $(document).ready(function() {
        $('#eventForm').on('submit', function(e) {
            alert('hdgx');
            e.preventDefault();
            $.ajax({
                url: "{{ url('/events') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
                    
                    $('#message').html('<div class="alert alert-success">Event created successfully!</div>');
                    $('#eventForm')[0].reset();
                    window.location.href = "{{ url('/events') }}";
                },
                error: function(xhr) {
                    $('#message').html('<div class="alert alert-danger">Error creating event.</div>');
                }
            });
        });
    });
</script>
@endsection