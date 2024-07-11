<!DOCTYPE html>
<html>

<head>
    <title>Order Updated</title>
</head>

<body>
    <h1>A new Order has been created!</h1>
    <p>Customer ame: {{ $order->customer_name }}</p>
    <p>Customer email : {{ $order->customer_email }}</p>
    <p>Product Price: {{ $order->total_amount }}</p>
    <p>Order Details:</p>

        <ul>
            <li>Order ID: {{ $order->id }}</li>
            @foreach ($order->products as $item)
                <li>{{ $item->name }} - {{ $item->pivot->quantity }} - ${{ $item->pivot->price }}</li>
            @endforeach
        </ul>

</body>

</html>
