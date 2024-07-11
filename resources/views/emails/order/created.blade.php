<!DOCTYPE html>
<html>
<head>
    <title>Product Created</title>
</head>
<body>
    <h1>A new Order has been created!</h1>
    <p>Customer ame: {{ $order->name }}</p>
    <p>Customer email : {{ $order->email }}</p>
    <p>Product Price: {{ $order->total_amount }}</p>
</body>
</html>
