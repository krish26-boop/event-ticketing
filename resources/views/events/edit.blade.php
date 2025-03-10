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

        <h4 class="mb-3">Ticket Price:</h4>
        <div class="row justify-content-center mb-5">
            <div class="col-md-4">
                <div class="card text-center shadow-lg">
                    <div class="card-header bg-info text-white">
                        <h4>Early Bird</h4>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">
                            <input type="number" id="early_price" name="early_price" placeholder="price" value="{{ $event->tickets[0]->price ?? '0.00'}}" class="form-control" required>
                            <input type="number" id="early_quantity" name="early_quantity" placeholder="quantity" value="{{ $event->tickets[0]->quantity ?? 0}}" class="form-control mt-3" required>
                        </h2>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-lg">
                    <div class="card-header bg-success  text-white">
                        <h4>Regular</h4>
                    </div>
                    <div class="card-body">
                        <input type="number" id="regular_price" name="regular_price" placeholder="price"  value="{{ $event->tickets[1]->price ?? '0.00'}}" class="form-control" required>
                        <input type="number" id="regular_quantity" name="regular_quantity" placeholder="quantity" value="{{ $event->tickets[1]->quantity ?? 0}}" class="form-control mt-3" required>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center shadow-lg">
                    <div class="card-header bg-danger  text-white">
                        <h4>VIP</h4>
                    </div>
                    <div class="card-body">
                        <input type="number" id="vip_price" name="vip_price" placeholder="price" value="{{ $event->tickets[2]->price ?? '0.00' }}" class="form-control" required>
                        <input type="number" id="vip_quantity" name="vip_quantity" placeholder="quantity" value="{{ $event->tickets[2]->quantity ?? 0}}" class="form-control mt-3" required>
                    </div>
                </div>
            </div>

        </div>

        <button type="submit" class="btn btn-primary">Update Event</button>
    </form>
    <div id="message" class="mt-3"></div>
</div>
@endsection

@section('Scripts')
<script>
    $(document).ready(function() {
        $('#editEventForm').on('submit', function(e) {
            e.preventDefault();
            var eventId = $('#event_id').val();
            $.ajax({
                url: "{{ url('/events/') }}" + "/" + eventId,
                type: "PUT",
                data: $(this).serialize(),
                success: function(response) {
                    $('#message').html('<div class="alert alert-success">Event updated successfully!</div>');
                    window.location.href = "{{ url('/events') }}";
                },
                error: function(xhr) {
                    $('#message').html('<div class="alert alert-danger">Error updating event.</div>');
                }
            });
        });

        $("#add-ticket-form").on('submit', function(e) {
            e.preventDefault();

            let eventId = $("#event_id").val();
            let type = $("#ticket-type").val();
            let price = $("#ticket-price").val();
            let quantity = $("#ticket-quantity").val();

            $.ajax({
                url: "{{ url('/tickets') }}",
                type: "POST",
                data: {
                    event_id: eventId,
                    type: type,
                    price: price,
                    quantity: quantity
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {

                    $("#ticketModal").modal('hide');

                    alert("Ticket added successfully!");
                    $("#add-ticket-form")[0].reset();
                    window.location.href = "{{ url('/tickets') }}";

                }
            });
        });
    });
</script>
@endsection