<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #000000E0;
            opacity: 1;
            font-size: 12px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-table td,
        th {
            padding: 10px;
            border-top: 1px solid #0000000F;
            border-bottom: 1px solid #0000000F;
        }

        .invoice-table th:first-child,
        td:first-child {
            border-left: 1px solid #0000000F;
        }

        .invoice-table th:last-child,
        td:last-child {
            border-right: 1px solid #0000000F;
        }

        .invoice-table th {
            background-color: #FAFAFA;
        }

        .invoice-table tfoot td {
            border: none;
        }

        .invoice-total {
            text-align: right;
        }

        .invoice-table-detail {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .invoice-table-detail td {
            padding: 10px;
        }

        .invoice-table-detail td {
            border-top: 1px solid #E8E8E8;
            border-bottom: 1px solid #E8E8E8;
        }

        .invoice-table-detail td {
            border-left: none;
            border-right: none;
        }

        .invoice-table-detail tr:nth-child(even) {
            background-color: #FAFAFA;
        }

        .invoice-table-detail tr:last-child {
            border-bottom: 0px;
        }

        .invoice-table-detail td:last-child {
            border-right: 0px;
            border-left: 0px;
            border-bottom: 0px;
        }

        .size-first-column {
            width: 65%;
        }

        .size-second-column {
            width: 35%;
            text-align: end;
        }

        .paddingTop24 {
            padding-top: 24px;
        }

        .padding12 {
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .col-content-center {
            text-align: center;
        }

        .col-content-right {
            text-align: right;
        }

        .footer-text {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            padding: 10px;
            background-color: #E8E8E8;
            border-top: 1px solid #0000000F;
            width: 100%;
        }
    </style>
</head>

<body>
    <div >
        <h2>Encomenda Nº {{ $id }}</h2>
        <table class="invoice-table-detail">
            <tr>
                <td class="size-first-column">
                    <span>Nome:</span>
                    <span>{{ $client['name'] }}</span>
                </td>
                <td class="size-second-column">
                    <span>Nº Requisição:</span>
                    <span>{{ $request_number }}</span>
                </td>
            </tr>
            <tr>
                <td class="size-first-column">
                    <span>Contacto:</span>
                    <span>{{ $client['contact'] ?? $caller_phone }}</span>
                </td>
                <td class="size-second-column">
                    <span>Data entrega:</span>
                    <span>{{ $delivery_date }}</span>
                </td>
            </tr>
            <tr>
                <td class="size-first-column">
                    <span>ID Primavera:</span>
                    <span>{{ $client['erp_client_id'] }}</span>
                </td>
                <td class="size-second-column">
                    <span>Zona:</span>
                    <span>{{ $zone }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span>Morada:</span>
                    <span> {{ $delivery_address ?? $client['address'] }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="paddingTop24">
                        <span><strong>Notas da Encomenda</strong></span>
                        <p>{{ $notes }}</p>
                    </div>
                </td>
            </tr>
        </table>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Enc</th>
                    <th>Qtd</th>
                    <th>UN</th>
                    <th>Volume</th>
                    <th>Preço UN</th>
                    <th>Desconto</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ $product['quantity'] . $product['sale_unit'] }}</td>
                    <td class="col-content-right">{{ $product['conversion'] }}</td>
                    <td class="col-content-center">{{ $product['unit'] }}</td>
                    <td class="col-content-right">{{ $product['volume'] }}</td>
                    <td class="col-content-center">{{ $product['unit_price'] . '€' }}</td>
                    <td class="col-content-center">{{ $product['discount'] . '%' }}</td>
                    <td class="col-content-center">{{ $product['total'] . '€' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="invoice-total" colspan="8">
                        <div class="paddingTop24">
                            <strong>Total a faturar:</strong>
                            {{ $total . '€' }}
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
    <span class="footer-text">Este documento não é uma factura e nem uma guia de transporte. Destina-se exclusivamente para uso interno e controlo administrativo.</span>
</body>

</html>