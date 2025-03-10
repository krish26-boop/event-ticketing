@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1>Welcome, {{ auth()->user()->name }}!</h1>
<p>Your role: {{ auth()->user()->getRoleNames() }}</p>

@role('attendee')
<h2>Upcoming Events</h2>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Title</th>
      <th scope="col">Date</th>
      <th scope="col">Location</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody id="upcomingEvents">
  </tbody>
  </table>
<!-- <div id="upcomingEvents"></div> -->

<input type="text" id="searchKeyword" placeholder="Search events...">
<input type="text" id="searchLocation" placeholder="Enter location...">
<input type="date" id="searchDate">
<button id="searchBtn">Search</button>
<div id="eventResults"></div>

@section('Scripts')
<script>
       $('#searchBtn').on('click', function() {
        let keyword = $('#searchKeyword').val();
        let location = $('#searchLocation').val();
        let date = $('#searchDate').val();

        $.ajax({
            url: "{{ url('/attendees/search') }}",
            method: 'GET',
            data: { keyword, location, date },
            success: function(response) {
                let eventsHtml = '';
                response.data.forEach(event => {
                    eventsHtml += `<div>
                        <h3>${event.title}</h3>
                        <p>${event.description}</p>
                        <p><strong>Location:</strong> ${event.location}</p>
                        <p><strong>Date:</strong> ${event.date}</p>
                    </div>`;
                });
                $('#eventResults').html(eventsHtml);
            }
        });
    });

   $.ajax({
        url: "{{ url('/attendees/upcoming') }}",
        method: 'GET',
        success: function(response) {            
            let eventsHtml = '';
            response.data.forEach(event => {
                eventsHtml += `<tr>
                    <td><h3>${event.title}</h3></td>
                    <td><p>${event.date}</p></td>
                    <td><p>${event.location}</p></td>
                    <td><a href="{{ url('/events/${event.id}') }}">View</a></td>
                </tr>`;
            });
            $('#upcomingEvents').html(eventsHtml);
        }
    });
</script>
@endsection
@endrole


@endsection
