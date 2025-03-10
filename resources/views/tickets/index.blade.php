@extends('layouts.app')

@section('content')

@if ($message = Session::get('success'))

<div class="alert alert-success">

  <p>{{ $message }}</p>

</div>

@endif
<div class="container">
<div id="message" class="mt-3"></div>

    <h2>Manage Tickets</h2>
    
    <button class="btn btn-success mb-3" onclick="loadEvents()">Refresh Events</button>
    <a href="{{ route('tickets.create') }}"  class="btn btn-success mb-3" >
                   Create Tickets
                </a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Date</th>
                <th>Location</th>
                <th>Tickets</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="eventList">
            <!-- Events will be loaded here via AJAX -->
        </tbody>
    </table>
</div>
@endsection

@section('Scripts')
<script>
    $(document).ready(function () {
        loadEvents(); // Load events on page load

        function loadEvents() {
            $.ajax({
                url: "{{ route('tickets.index') }}",
                type: "GET",
                success: function (response) {                    
                    let eventsHtml = "";
                    $.each(response.events, function (index, event) {
                        eventsHtml += `
                            <tr id="eventRow-${event.id}">
                                <td>${event.title}</td>
                                <td>${event.description}</td>
                                <td>${event.date}</td>
                                <td>${event.location}</td>
                                <td>${event.tickets_available}</td>
                                <td>
                                    <a href="{{ url('/events/${event.id}/edit') }}" class="btn btn-primary btn-sm">Edit</a>
                                    <button onclick="deleteEvent(${event.id})" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#eventList').html(eventsHtml);
                },
                error: function () {
                    alert("Error loading events.");
                }
            });
        }

        window.deleteEvent = function (eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                $.ajax({
                    url: '{{ url("/events/") }}' + "/" + eventId,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        $(`#eventRow-${eventId}`).remove();
                    },
                    error: function () {
                        alert("Error deleting event.");
                    }
                });
            }
        }
    });
</script>
@endsection
