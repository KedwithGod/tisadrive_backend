<!DOCTYPE html>
<html>
<head>
    <title>PIX Payment</title>
    <style>
        body {
            font-family: Arial;
            text-align: center;
            padding: 50px;
        }
        .pix-box {
            border: 1px solid #ccc;
            padding: 30px;
            display: inline-block;
        }
        img {
            max-width: 300px;
        }
        .copy-code {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="pix-box">
        <h2>PIX Payment</h2>
        <p>Amount: {{ $currency }} {{ $amount }}</p>
        <p>Scan the QR Code below:</p>
        <img src="data:image/png;base64,{{ $payment->point_of_interaction->transaction_data->qr_code_base64 }}">

        <div class="copy-code">
            <p>Or copy the PIX code below:</p>
            <textarea id="pixCode" readonly rows="4" cols="50">{{ $payment->point_of_interaction->transaction_data->qr_code }}</textarea><br>
            <button onclick="copyPixCode()">Copy Code</button>
        </div>
    </div>

    <script>
        function copyPixCode() {
            var copyText = document.getElementById("pixCode");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("PIX Code copied!");
        }
    </script>
</body>
</html>
