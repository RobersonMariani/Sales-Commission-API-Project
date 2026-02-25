<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumo Administrativo</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { background: #059669; color: #fff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { padding: 24px; }
        .summary-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .summary-table td { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; }
        .summary-table td:first-child { font-weight: 600; color: #374151; }
        .summary-table td:last-child { text-align: right; color: #111827; }
        .highlight { background: #ecfdf5; font-size: 18px; font-weight: 700; color: #059669; }
        .footer { padding: 16px 24px; text-align: center; font-size: 12px; color: #9ca3af; background: #f9fafb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Resumo Administrativo de Vendas</h1>
        </div>
        <div class="content">
            <p>Resumo geral das vendas do dia <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>:</p>

            <table class="summary-table">
                <tr>
                    <td>Vendedores Ativos no Dia</td>
                    <td>{{ $sellersCount }}</td>
                </tr>
                <tr>
                    <td>Quantidade de Vendas</td>
                    <td>{{ $salesCount }}</td>
                </tr>
                <tr class="highlight">
                    <td>Valor Total das Vendas</td>
                    <td>R$ {{ number_format($totalSales, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            <p>{{ config('app.name') }} &mdash; {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</p>
        </div>
    </div>
</body>
</html>
