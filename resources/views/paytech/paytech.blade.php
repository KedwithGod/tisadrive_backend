<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Paytech</title>
    <style>
        body {
            font-size: 14px;
            font-family: "Moderat","Inter",sans-serif;
            font-weight: 400;
            color: #333;
        }
        .center{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        #start-payment-button {
            background: #0a8708;
            color: #ffffff;
            padding: 10px;
            font-size:16px;
            border: 1px solid #0a8708;
            border-radius: 10px;
        }
    </style>

</head>
<body>
    <div class="center">
        <img src="{{ asset('assets/img/paytech.png')}}" class="img-fluid">
        <h1>{{ $amount }} {{ $currency_code }}</h1>
        <form id="paymentForm" action="{{ route('paytech.initiate') }}" method="POST">
            @csrf
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="currency" value="{{ $currency_code }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="plan_id" value="{{ $plan_id }}">
            <input type="hidden" name="request_id" value="{{ $request_id }}">
            <input type="hidden" name="payment_for" value="{{ $payment_for }}">
            </br>

            @if($payment_for=="wallet")
            <button type="submit" id="start-payment-button">Add To Wallet</button>
            @else
            <button type="submit" id="start-payment-button">Pay</button>
            @endif
        </form>

    </div>
</body>
</html>
