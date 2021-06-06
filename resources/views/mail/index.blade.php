<h2>Você recebeu uma transferência.</h2>

<p>
    Olá, {{ $transactionDto->getPayeeName() }}. <br><br>

    Você recebeu uma transferência no valor de {{ number_format($transactionDto->getValue(), 2, ',', '.') }}, enviada por {{ $transactionDto->getPayerName() }}.
</p>
