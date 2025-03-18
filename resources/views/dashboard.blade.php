@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1>Welcome, {{ auth()->user()->name }}!</h1>
<p class="mb-4">Your role: {{ auth()->user()->getRoleNames()->first() }}</p>

@role('attendee')
<h2 class="mb-3 ">Upcoming Events</h2>

<form id="searchForm" class="mb-3">
    <input type="text" class="me-2" id="searchKeyword" name="keyword" placeholder="Search by keyword">
    <input type="text" class="me-2" id="searchLocation" name="location" placeholder="Search by location">
    <input type="date" class="me-3" id="searchDate" name="date">
    <button type="submit"  class="btn btn-success">Search</button>
    <button type="reset"  class="btn btn-danger" onclick="fetchEvents()">Reset</button>
</form>


<table class="table mb-5">
  <thead>
    <tr>
      <th class="bg-info" scope="col">Title</th>
      <th class="bg-info" scope="col">Date</th>
      <th class="bg-info" scope="col">Location</th>
      <th class="bg-info" scope="col">Action</th>
    </tr>
  </thead>
  <tbody id="upcomingEvents">
  </tbody>
  </table>
<!-- <div id="upcomingEvents"></div> -->


<div id="eventResults"></div>

@section('Scripts')
<script>
    $(document).ready(function () {
        window.fetchEvents = function (data = {}) {
        $.ajax({
            url: "{{ url('/attendees/upcoming') }}",
            method: 'GET',
            data: data,
            success: function (response) {
                let eventsHtml = '';
                if (response.data.length > 0) {
                    response.data.forEach(event => {
                        eventsHtml += `<tr>
                            <td><h3>${event.title}</h3></td>
                            <td><p>${event.date}</p></td>
                            <td><p>${event.location}</p></td>
                            <td><a href="{{ url('/events/${event.id}') }}" class="btn btn-dark">View</a></td>
                        </tr>`;
                    });
                } else {
                    eventsHtml = `<tr><td colspan="4">No events found</td></tr>`;
                }
                $('#upcomingEvents').html(eventsHtml);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    // Fetch all events on page load
    fetchEvents();

    // Search button click event
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        fetchEvents(formData);
    });
});

</script>
@endsection
@endrole


@endsection
