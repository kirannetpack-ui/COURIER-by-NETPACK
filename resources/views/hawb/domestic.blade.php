<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domestic HAWB - {{ $shipment->tracking_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }
        body {
            font-family: 'Courier New', 'Arial', sans-serif;
            margin: 0;
            padding: 5mm;
            background: #f0f0f0;
        }
        .hawb-page {
            max-width: 100%;
            background: white;
            padding: 6mm 8mm;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .hawb-container {
            border: 1px solid #333;
            border-radius: 2px;
            padding: 6mm 8mm;
            margin-bottom: 8mm;
            page-break-inside: avoid;
            background: white;
        }
        .hawb-container:last-child {
            margin-bottom: 0;
        }
        /* Header */
        .hawb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
        .hawb-header .logo {
            font-size: 20px;
            font-weight: bold;
            color: #0d9488;
            letter-spacing: 1px;
        }
        .hawb-header .logo span {
            color: #1e293b;
        }
        .hawb-badge {
            background: #0d9488;
            color: white;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .hawb-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #1e293b;
            margin: 2px 0 4px 0;
            text-transform: uppercase;
        }
        .hawb-title .sub {
            font-size: 9px;
            color: #64748b;
            font-weight: normal;
            letter-spacing: 1px;
        }
        /* Tracking Number */
        .tracking-number {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 4px;
            background: #f8fafc;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            margin: 4px 0 6px 0;
            color: #0f172a;
        }
        /* QR + Info Row */
        .qr-info-row {
            display: flex;
            gap: 10px;
            margin: 4px 0 6px 0;
            align-items: stretch;
        }
        .qr-section {
            flex: 0 0 auto;
            text-align: center;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px 6px;
            background: #fafafa;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 100px;
        }
        .qr-section img {
            width: 100px;
            height: 100px;
            display: block;
        }
        .qr-section .qr-label {
            font-size: 7px;
            color: #94a3b8;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }
        .info-cards {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 4px;
        }
        .info-card {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px 6px;
            background: #fafafa;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .info-card .label {
            font-size: 7px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .info-card .value {
            font-size: 11px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 1px;
        }
        .service-badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }
        .service-badge.flash { background: #fef2f2; color: #dc2626; }
        .service-badge.same_day { background: #fffbeb; color: #d97706; }
        .service-badge.standard { background: #eff6ff; color: #2563eb; }
        .service-badge.himalayan { background: #faf5ff; color: #7c3aed; }
        /* Sender/Receiver */
        .party-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin: 4px 0 6px 0;
        }
        .party-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 4px 6px;
            background: #fafafa;
        }
        .party-box .label {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .party-box .label.shipper { color: #0d9488; }
        .party-box .label.consignee { color: #2563eb; }
        .party-box .name {
            font-size: 11px;
            font-weight: 600;
            color: #0f172a;
            margin-top: 1px;
        }
        .party-box .address {
            font-size: 9px;
            color: #475569;
            margin-top: 1px;
            line-height: 1.3;
        }
        .party-box .phone {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
        }
        /* Footer */
        .footer {
            margin-top: 4px;
            padding-top: 4px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 7px;
            color: #94a3b8;
        }
        .footer .company {
            font-weight: 600;
            color: #0f172a;
        }
        .footer .terms {
            text-align: right;
            font-size: 6.5px;
            line-height: 1.3;
        }
        .hawb-id {
            font-size: 7px;
            color: #94a3b8;
            text-align: right;
            margin-top: 2px;
            border-top: 1px dotted #e2e8f0;
            padding-top: 2px;
        }
        /* Divider between HAWBs */
        .hawb-divider {
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
            padding: 2px 0;
            border-bottom: 1px dashed #e2e8f0;
            margin-bottom: 8mm;
        }
        @media print {
            body {
                background: white;
                padding: 3mm;
            }
            .hawb-page {
                box-shadow: none;
                padding: 0;
            }
            .hawb-container {
                border-color: #ccc;
                margin-bottom: 6mm;
            }
            .hawb-divider {
                border-bottom-color: #ccc;
            }
        }
        @media (max-width: 600px) {
            .info-cards {
                grid-template-columns: 1fr 1fr;
            }
            .party-section {
                grid-template-columns: 1fr;
            }
            .qr-info-row {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="hawb-page">
        <!-- HAWB 1 -->
        <div class="hawb-container">
            <!-- Header -->
            <div class="hawb-header">
                <div class="logo">NET<span>PACK</span></div>
                <span class="hawb-badge">DOMESTIC</span>
            </div>

            <!-- Title -->
            <div class="hawb-title">
                DOMESTIC AIR WAYBILL
                <div class="sub">Domestic Shipment Document</div>
            </div>

            <!-- Tracking Number -->
            <div class="tracking-number">
                {{ $shipment->tracking_number }}
            </div>

            <!-- QR + Info -->
            <div class="qr-info-row">
                <div class="qr-section">
                    {!! $qrCode !!}
                    <div class="qr-label">Scan to Track</div>
                </div>
                <div class="info-cards">
                    <div class="info-card">
                        <div class="label">Service</div>
                        <div class="value">
                            <span class="service-badge {{ $shipment->service_type ?? 'standard' }}">
                                {{ strtoupper($shipment->service_type ?? 'STANDARD') }}
                            </span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="label">Type</div>
                        <div class="value">{{ strtoupper($shipment->shipment_type ?? 'Parcel') }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Weight</div>
                        <div class="value">{{ number_format($shipment->weight ?? 0, 2) }} kg</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Status</div>
                        <div class="value" style="color: {{ $shipment->status === 'delivered' ? '#10b981' : ($shipment->status === 'pending' ? '#f59e0b' : '#3b82f6') }}">
                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sender & Receiver -->
            <div class="party-section">
                <div class="party-box">
                    <div class="label shipper">SHIPPER / SENDER</div>
                    <div class="name">{{ $shipment->sender_name ?? 'N/A' }}</div>
                    <div class="address">
                        {{ $shipment->sender_address ?? '' }}<br>
                        {{ $shipment->sender_city ?? '' }}, {{ $shipment->sender_zone ?? '' }}
                    </div>
                    <div class="phone"><i class="fas fa-phone"></i> {{ $shipment->sender_phone ?? 'N/A' }}</div>
                </div>
                <div class="party-box">
                    <div class="label consignee">CONSIGNEE / RECEIVER</div>
                    <div class="name">{{ $shipment->receiver_name ?? 'N/A' }}</div>
                    <div class="address">
                        {{ $shipment->receiver_address ?? '' }}<br>
                        {{ $shipment->receiver_city ?? '' }}, {{ $shipment->receiver_zone ?? '' }}
                    </div>
                    <div class="phone">
                        <i class="fas fa-phone"></i> {{ $shipment->receiver_phone ?? 'N/A' }}
                        @if($shipment->receiver_ward)
                            <br><i class="fas fa-map-pin"></i> Ward: {{ $shipment->receiver_ward }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div>
                    <span class="company">NETPACK Logistics</span><br>
                    Kathmandu, Nepal
                </div>
                <div class="terms">
                    <div style="font-weight:600; color:#0f172a;">Terms & Conditions</div>
                    This document is subject to NETPACK's terms of carriage.<br>
                    support@netpack.com
                </div>
            </div>

            <div class="hawb-id">
                HAWB #{{ $shipment->id }} | Generated: {{ now()->format('d M Y H:i') }}
            </div>
        </div>

        <!-- Divider -->
        <div class="hawb-divider">— — — — — — — — — — — — — — — — — — — — — — — — — — — — — —</div>

        <!-- HAWB 2 -->
        <div class="hawb-container">
            <!-- Header -->
            <div class="hawb-header">
                <div class="logo">NET<span>PACK</span></div>
                <span class="hawb-badge">DOMESTIC</span>
            </div>

            <!-- Title -->
            <div class="hawb-title">
                DOMESTIC AIR WAYBILL
                <div class="sub">Domestic Shipment Document</div>
            </div>

            <!-- Tracking Number -->
            <div class="tracking-number">
                {{ $shipment->tracking_number }}
            </div>

            <!-- QR + Info -->
            <div class="qr-info-row">
                <div class="qr-section">
                    {!! $qrCode !!}
                    <div class="qr-label">Scan to Track</div>
                </div>
                <div class="info-cards">
                    <div class="info-card">
                        <div class="label">Service</div>
                        <div class="value">
                            <span class="service-badge {{ $shipment->service_type ?? 'standard' }}">
                                {{ strtoupper($shipment->service_type ?? 'STANDARD') }}
                            </span>
                        </div>
                    </div>
                    <div class="info-card">
                        <div class="label">Type</div>
                        <div class="value">{{ strtoupper($shipment->shipment_type ?? 'Parcel') }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Weight</div>
                        <div class="value">{{ number_format($shipment->weight ?? 0, 2) }} kg</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Status</div>
                        <div class="value" style="color: {{ $shipment->status === 'delivered' ? '#10b981' : ($shipment->status === 'pending' ? '#f59e0b' : '#3b82f6') }}">
                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sender & Receiver -->
            <div class="party-section">
                <div class="party-box">
                    <div class="label shipper">SHIPPER / SENDER</div>
                    <div class="name">{{ $shipment->sender_name ?? 'N/A' }}</div>
                    <div class="address">
                        {{ $shipment->sender_address ?? '' }}<br>
                        {{ $shipment->sender_city ?? '' }}, {{ $shipment->sender_zone ?? '' }}
                    </div>
                    <div class="phone"><i class="fas fa-phone"></i> {{ $shipment->sender_phone ?? 'N/A' }}</div>
                </div>
                <div class="party-box">
                    <div class="label consignee">CONSIGNEE / RECEIVER</div>
                    <div class="name">{{ $shipment->receiver_name ?? 'N/A' }}</div>
                    <div class="address">
                        {{ $shipment->receiver_address ?? '' }}<br>
                        {{ $shipment->receiver_city ?? '' }}, {{ $shipment->receiver_zone ?? '' }}
                    </div>
                    <div class="phone">
                        <i class="fas fa-phone"></i> {{ $shipment->receiver_phone ?? 'N/A' }}
                        @if($shipment->receiver_ward)
                            <br><i class="fas fa-map-pin"></i> Ward: {{ $shipment->receiver_ward }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div>
                    <span class="company">NETPACK Logistics</span><br>
                    Kathmandu, Nepal
                </div>
                <div class="terms">
                    <div style="font-weight:600; color:#0f172a;">Terms & Conditions</div>
                    This document is subject to NETPACK's terms of carriage.<br>
                    support@netpack.com
                </div>
            </div>

            <div class="hawb-id">
                HAWB #{{ $shipment->id }} | Generated: {{ now()->format('d M Y H:i') }}
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div style="text-align: center; margin-top: 10px; padding: 10px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-width: 210mm; margin-left: auto; margin-right: auto;">
        <button onclick="window.print()" style="background: #0d9488; color: white; border: none; padding: 10px 40px; border-radius: 6px; font-size: 14px; cursor: pointer; font-weight: 600;">
            🖨 Print HAWB (2 per page - Vertical)
        </button>
        <button onclick="window.location.href='{{ route('tracking.show', $shipment->tracking_number) }}'" 
                style="background: #1e293b; color: white; border: none; padding: 10px 40px; border-radius: 6px; font-size: 14px; cursor: pointer; margin-left: 10px; font-weight: 600;">
            📦 Back to Tracking
        </button>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>