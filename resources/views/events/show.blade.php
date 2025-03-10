@extends('layouts.app')

@section('content')
<div class="container">
    <h2>View Event</h2>

    <!-- Event Details -->
    <div class="card shadow-lg mb-4">
        <div class="card-body">
            <h2 class="card-title">{{ $event->title }}</h2>
            <p class="text-muted">Date: {{ $event->date }}</p>
            <p><strong>Location:</strong> {{ $event->location }}</p>
            <p class="card-text">{{ $event->description }}</p>
        </div>
    </div>

    <!-- Ticket Information -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-primary text-white">Tickets</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Availability</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->type }}</td>
                        <td>{{ $ticket->price }}</td>
                        <td>{{ ($ticket->quantity) ? 'Available' : 'Sold Out' }}</td>
                        <td><button class="btn btn-success"
                                @if($ticket->quantity)
                                onclick="showBuyTicketModal('{{ $ticket->id }}','{{ $ticket->type }}', {{ $ticket->price }})"
                                @else
                                disabled
                                @endif>
                                {{ $ticket->quantity ? 'Buy Now' : 'Sold Out' }}
                            </button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="card shadow-lg">
        <div class="card-header bg-secondary text-white">Questions & Comments</div>
        <div class="card-body">
            @auth
            <div class="mb-3">
                <textarea class="form-control" id="comment-text" rows="3" placeholder="Ask a question or leave a comment..."></textarea>
            </div>
            <button class="btn btn-primary" onclick="submitComment()">Submit</button>
            @else
            <p><a href="{{ route('login') }}">Login</a> to leave a comment.</p>
            @endauth
            <hr>
            <div id="comments-section">
                <!-- Comments will load here -->
            </div>
            <!-- Sample Comments -->
            <!-- <div class="mb-3">
                    <strong>John Doe</strong>
                    <p>Will there be parking available near the venue?</p>
                    <small class="text-muted">2 hours ago</small>
                </div>
                <div class="mb-3">
                    <strong>Jane Smith</strong>
                    <p>Are outside food and drinks allowed?</p>
                    <small class="text-muted">1 hour ago</small>
                </div> -->
        </div>
    </div>


</div>

<!-- Buy Ticket Modal -->
<div class="modal fade" id="buyTicketModal" tabindex="-1" aria-labelledby="buyTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buyTicketModalLabel">Buy Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="checkout-form">
                    <input type="hidden" class="form-control" id="ticketId" readonly>
                    <div class="mb-3">
                        <label for="ticketType" class="form-label">Ticket Type</label>
                        <input type="text" class="form-control" id="ticketType" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="ticketPrice" class="form-label">Price</label>
                        <input type="text" class="form-control" id="ticketPrice" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="ticketQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="ticketQuantity" value="1" min="1" max="10" onchange="updateTotalPrice()">
                    </div>
                    <div class="mb-3">
                        <label for="totalPrice" class="form-label">Total Price</label>
                        <input type="text" class="form-control" id="totalPrice" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="proceedToCheckout()">Proceed to Checkout</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('Scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        loadComments();
    });

    function loadComments() {
        fetch("{{ url('/comments') }}/{{ $event->id }}")
            .then(response => response.json())
            .then(comments => {
                let commentsHTML = '';
                comments.forEach(comment => {
                    commentsHTML += `<div class="mb-3">
                        <strong>${comment.user.name}</strong>
                        <p>${comment.comment}</p>
                        <small class="text-muted">${new Date(comment.created_at).toLocaleString()}</small>
                    </div>`;
                });
                document.getElementById("comments-section").innerHTML = commentsHTML;
            });
    }

    function submitComment() {
        let comment = document.getElementById("comment-text").value;

        fetch("{{ url('/comments') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    event_id: '{{ $event->id }}',
                    comment: comment
                })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                document.getElementById("comment-text").value = "";
                loadComments();
            });
    }

    function showBuyTicketModal(id,type, price) {
        document.getElementById("ticketId").value = id;
        document.getElementById("ticketType").value = type;
        document.getElementById("ticketPrice").value = price;
        document.getElementById("ticketQuantity").value = 1;
        document.getElementById("totalPrice").value = price;
        new bootstrap.Modal(document.getElementById('buyTicketModal')).show();
    }

    function updateTotalPrice() {
        let price = parseFloat(document.getElementById("ticketPrice").value.replace("$", ""));
        let quantity = parseInt(document.getElementById("ticketQuantity").value);
        document.getElementById("totalPrice").value = (price * quantity).toFixed(2);
    }

    function proceedToCheckout() {
        let formData = {
            tickets: [
                {
            ticket_id: document.getElementById("ticketId").value,
            ticket_type: document.getElementById("ticketType").value,
            ticket_price: document.getElementById("ticketPrice").value,
            ticket_quantity: document.getElementById("ticketQuantity").value,
        } ]
    };

        fetch("{{ url('/attendees/checkout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    alert(data.message);
                    // location.reload(); // Refresh page after successful checkout
                }
            }).catch(error => console.error('Error processing checkout:', error));
    }
</script>
@endsection