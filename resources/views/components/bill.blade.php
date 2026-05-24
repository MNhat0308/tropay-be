<!doctype html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <style>
        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-Bold.ttf") }}) format("truetype");
            font-weight: 700;
            font-style: normal;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-BoldItalic.ttf") }}) format("truetype");
            font-weight: 700;
            font-style: italic;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-ExtraBold.ttf") }}) format("truetype");
            font-weight: 800;
            font-style: normal;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-ExtraBoldItalic.ttf") }}) format("truetype");
            font-weight: 800;
            font-style: italic;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-Light.ttf") }}) format("truetype");
            font-weight: 300;
            font-style: normal;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-LightItalic.ttf") }}) format("truetype");
            font-weight: 300;
            font-style: italic;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-Medium.ttf") }}) format("truetype");
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-MediumItalic.ttf") }}) format("truetype");
            font-weight: 500;
            font-style: italic;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-Regular.ttf") }}) format("truetype");
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-SemiBold.ttf") }}) format("truetype");
            font-weight: 600;
            font-style: normal;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-SemiBoldItalic.ttf") }}) format("truetype");
            font-weight: 600;
            font-style: italic;
        }

        @font-face {
            font-family: 'Open Sans';
            src: url({{ storage_path("fonts/OpenSans/static/OpenSans-Italic.ttf") }}) format("truetype");
            font-weight: 400;
            font-style: italic;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: #333;
        }

        h4 {
            margin: 0;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
        }

        .margin-top {
            margin-top: 1.25rem;
        }

        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.products {
            font-size: 0.875rem;
        }

        table.products tr {
            background-color: rgb(96 165 250);
        }

        table.products th {
            color: #ffffff;
            padding: 0.5rem;
        }

        table tr.items {
            background-color: rgb(241 245 249);
        }

        table tr.items td {
            padding: 0.5rem;
        }

        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }

        .primary {
            color: #91e38b;
        }

    </style>
</head>
<body>
<div style="text-align: center">
    <h2>Phiếu thu tiền trọ tháng {{$record->rent_month}}</h2>
</div>

<div class="margin-top">
    <table class="w-full">
        <tr>
            <td class="w-half">
                    <div>{{$record->room()->first()->name}}</div>
                    <div>Thời gian : {{$record->at->format('d/m/Y')}}.</div>
            </td>
            <td class="w-half">
                    <div class="primary">
                        Tổng tiền: {{ Number::format($record->total_price) }} VNĐ
                    </div>
            </td>
        </tr>
    </table>
</div>

<div class="margin-top">
    <table class="products">
        <tr>
            <th>Loại</th>
            <th>Giá thành</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
        </tr>
        <tr class="items">
            <td>
                Nước
            </td>
            <td>
                {{ Number::format($record->price_water)  }}
            </td>
            <td>
                {{ $record->water_consumption }}
            </td>
            <td class="primary">
                {{ Number::format($record->price_water * $record->water_consumption) }}
            </td>
        </tr>

        @php
            $cons = floatval($record->electric_consumption);
            $basePrice = floatval($record->price_electric);
            $elecCost = $cons * $basePrice;
            $elecDetail = "Chỉ số: {$record->old_electric} → {$record->new_electric} = {$cons} kWh";
            $elecUnitPriceText = Number::format($basePrice);
        @endphp
        <tr class="items">
            <td>
                Điện<br/>
                <span style="font-size: 10px; color: #64748b; font-weight: normal; white-space: pre-line;">{!! nl2br(e($elecDetail)) !!}</span>
            </td>
            <td>
                {{ $elecUnitPriceText }}
            </td>
            <td>
                {{ $record->electric_consumption }}
            </td>
            <td class="primary">
                {{ Number::format($elecCost) }}
            </td>
        </tr>
        <tr class="items">
            <td>
                Rác
            </td>
            <td>
                {{ Number::format($record->price_garbage) }}
            </td>
            <td>
                1
            </td>
            <td class="primary">
                {{ Number::format($record->price_garbage) }}
            </td>
        </tr>
        <tr class="items">
            <td>
                Phòng
            </td>
            <td>
                {{ Number::format($record->price_room) }}
            </td>
            <td>
                1
            </td>
            <td class="primary">
                {{ Number::format($record->price_room) }}
            </td>
        </tr>
        <tr class="items">
            <td>
                Khác
            </td>
            <td>
            </td>
            <td>
            </td>
            <td>
            </td>
        </tr>
    </table>

    <table class="products">
        <tr>
            <th>Loại</th>
            <th>Tháng trước</th>
            <th>Tháng này</th>
            <th>Số lượng tiêu thụ</th>
        </tr>
        <tr class="items">
            <td>
                Nước
            </td>
            <td>
                {{ $record->old_water }}
            </td>
            <td>
                {{ $record->new_water }}
            </td>
            <td>
                {{ $record->water_consumption }}
            </td>
        </tr>

        <tr class="items">
            <td>
                Điện
            </td>
            <td>
                {{ $record->old_electric }}
            </td>
            <td>
                {{ $record->new_electric }}
            </td>
            <td>
                {{ $record->electric_consumption }}
            </td>
        </tr>


    </table>
</div>

<div class="total">
    Tổng cộng tiền: <span class="primary">{{Number::format($record->total_price)  }}</span> VNĐ
</div>

<div class="footer margin-top">
    <div>ĐOÀN VĂN CƯỜNG</div>
    <div><a href="tel:0985626739">0985626739</a></div>
    <div> 44/24/8 Tăng Nhơn Phú, Phường Phước Long B, Tp Thủ Đức, TP HCM</div>
</div>
</body>
</html>
