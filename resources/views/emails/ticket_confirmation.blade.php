<!DOCTYPE html>
<html>
<head>
    <title>Ticket Purchase Confirmation</title>
</head>
<body>
    <h2>Thank you for your purchase, {{ $user->name }}!</h2>
    <p>Your order ID: <strong>#{{ $order->id }}</strong></p>
    <p>Total Amount: <strong>${{ number_format($order->total_amount, 2) }}</strong></p>

    <h3>Tickets:</h3>
    <ul>
        @foreach($orderItems as $item)
            <li>Ticket ID: {{ $item['ticket_id'] }}</li>
            <li>Ticket Type: {{ $item['type'] }}</li>
            <li>Quantity: {{ $item['quantity'] }}</li>
            <li>Price:  {{ number_format($item['price'], 2) }}</li>
        @endforeach
    </ul>

    <p>We look forward to seeing you at the event!</p>
</body>
</html>
