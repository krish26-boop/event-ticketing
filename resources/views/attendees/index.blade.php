@extends('layouts.app')

@section('content')

@if ($message = Session::get('success'))

<div class="alert alert-success">

    <p>{{ $message }}</p>

</div>

@endif
<div class="container">
    <div id="message" class="mt-3"></div>

    <h2>Attendees</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Event Title</th>
                    <th>Attendee Name</th>
                    <th>Email</th>
                    <th>Ticket Type</th>
                    <th>Quantity</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
            <tbody id="attendeeList">
                <!-- Events will be loaded here via AJAX -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('Scripts')
<script>
    $(document).ready(function() {
        loadAttendees(); // Load Attendees on page load

        function loadAttendees() {
            $.ajax({
                url: "{{ route('attendees.index') }}",
                type: "GET",
                success: function(response) {
                    console.log(response);

                    let AttendeesHtml = "";
                    $.each(response.attendees, function(index, attendee) {
                        if (attendee.events.length > 0) {
                            $.each(attendee.events, function(eventIndex, event) {
                                if (event.orders.length > 0) {
                                    $.each(event.orders, function(orderIndex, order) {
                                        if (order.orderitems.length > 0) {
                                            $.each(order.orderitems, function(itemIndex, item) {

                                                if (item.tickets) {
                                                    console.log(response);
                                                    let purchaseDate = new Date(order.created_at).toISOString().split('T')[0];

                                                    AttendeesHtml += `
                                                    <tr id="attendeeRow-${attendee.id}">
                                                        <td>${index + 1}</td>
                                                        <td>${event.title}</td>
                                                        <td>${order.user.name}</td>
                                                        <td>${order.user.email}</td>
                                                        <td>${item.tickets[0].type}</td>
                                                        <td>${item.quantity}</td>
                                                        <td>${purchaseDate}</td>
                                                    </tr>
                                                `;
                                                }
                                            });
                                        }
                                    });
                                } else {
                                    AttendeesHtml += `
                            <tr id="attendeeRow-${attendee.id}">
                                <td>${index + 1}</td>
                                <td colspan="6">No attendees data available</td>
                            </tr>
                        `;
                                }
                            });
                        } else {
                            AttendeesHtml += `
                            <tr id="attendeeRow-${attendee.id}">
                                <td>${index + 1}</td>
                                <td colspan="6">No event data available</td>
                            </tr>
                        `;
                        }
                    });
                    $('#attendeeList').html(AttendeesHtml);
                },
                error: function() {
                    alert("Error loading Attendees.");
                }
            });
        }


        window.deleteEvent = function(eventId) {
            if (confirm("Are you sure you want to delete this event?")) {
                $.ajax({
                    url: '{{ url("/events/") }}' + "/" + eventId,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        $(`#eventRow-${eventId}`).remove();
                    },
                    error: function() {
                        alert("Error deleting event.");
                    }
                });
            }
        }
    });
</script>
@endsection