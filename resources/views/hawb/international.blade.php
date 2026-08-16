<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HAWB - {{ $shipment->tracking_number }}</title>
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
            position: relative;
        }
        .hawb-container:last-child {
            margin-bottom: 0;
        }
        /* Nepal Flag SVG Styling */
        .nepal-flag-container {
            position: absolute;
            top: 4mm;
            right: 6mm;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nepal-flag-svg {
            width: 50px;
            height: 35px;
            border-radius: 2px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .nepal-flag-text {
            font-family: 'Mangal', 'Arial', 'Courier New', sans-serif;
            font-size: 16px;
            font-weight: bold;
            color: #dc2626;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 1px;
            background: rgba(255,255,255,0.85);
            padding: 2px 10px;
            border-radius: 4px;
            border: 1px solid #dc2626;
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
            grid-template-columns: 1fr 1fr 1fr;
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
        /* Receiver box with Nepal flag watermark */
        .party-box.consignee-box {
            position: relative;
            overflow: hidden;
            border: 2px solid #2563eb;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }
        .party-box.consignee-box .flag-watermark {
            position: absolute;
            right: 4px;
            bottom: 4px;
            opacity: 0.12;
            pointer-events: none;
            font-size: 60px;
        }
        .party-box.consignee-box .jai-nepal-watermark {
            position: absolute;
            right: 12px;
            top: 4px;
            font-size: 14px;
            font-weight: bold;
            color: #dc2626;
            opacity: 0.15;
            font-family: 'Mangal', 'Arial', sans-serif;
            pointer-events: none;
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
            <!-- Nepal Flag with Text -->
            <div class="nepal-flag-container">
                <!-- Simple Nepal Flag SVG -->
                <svg class="nepal-flag-svg" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
                    <!-- Flag background (crimson red) -->
                    <polygon points="0,0 80,0 80,50 0,70" fill="#dc2626"/>
                    <!-- Blue border -->
                    <polygon points="2,2 76,2 76,48 2,66" fill="none" stroke="#1e3a8a" stroke-width="3"/>
                    <!-- Sun -->
                    <circle cx="30" cy="25" r="12" fill="white" stroke="#1e3a8a" stroke-width="1.5"/>
                    <circle cx="30" cy="25" r="8" fill="none" stroke="#dc2626" stroke-width="1.5"/>
                    <!-- Sun rays -->
                    <line x1="30" y1="10" x2="30" y2="15" stroke="#1e3a8a" stroke-width="2"/>
                    <line x1="30" y1="35" x2="30" y2="40" stroke="#1e3a8a" stroke-width="2"/>
                    <line x1="15" y1="25" x2="20" y2="25" stroke="#1e3a8a" stroke-width="2"/>
                    <line x1="40" y1="25" x2="45" y2="25" stroke="#1e3a8a" stroke-width="2"/>
                    <!-- Moon -->
                    <path d="M 50,45 Q 58,40 65,45 Q 58,50 50,45" fill="white" stroke="#1e3a8a" stroke-width="1.5"/>
                    <path d="M 53,44 Q 58,42 62,44 Q 58,46 53,44" fill="#dc2626" stroke="none"/>
                </svg>
                <span class="nepal-flag-text">जय नेपाल</span>
            </div>

            <!-- Header -->
            <div class="hawb-header">
                <div class="logo">NET<span>PACK</span></div>
                <span class="hawb-badge">INTERNATIONAL</span>
            </div>

            <!-- Title -->
            <div class="hawb-title">
                HOUSE AIR WAYBILL
                <div class="sub">International Shipment Document</div>
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
                        <div class="value">{{ strtoupper($shipment->service_type ?? 'Standard') }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Type</div>
                        <div class="value">{{ strtoupper($shipment->shipment_type ?? 'Parcel') }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Weight</div>
                        <div class="value">{{ number_format($shipment->chargeable_weight ?? 0, 2) }} kg</div>
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
                        {{ $shipment->sender_city ?? '' }}, {{ $shipment->sender_country ?? '' }}
                    </div>
                    <div class="phone"><i class="fas fa-phone"></i> {{ $shipment->sender_phone ?? 'N/A' }}</div>
                </div>
                <div class="party-box consignee-box">
                    <div class="flag-watermark">🇳🇵</div>
                    <div class="jai-nepal-watermark">जय नेपाल</div>
                    <div class="label consignee">CONSIGNEE / RECEIVER</div>
                    <div class="name">{{ $shipment->receiver_name ?? 'N/A' }}</div>
                    <div class="address">
                        {{ $shipment->receiver_address ?? '' }}<br>
                        {{ $shipment->receiver_city ?? '' }}, {{ $shipment->receiver_country ?? '' }}
                    </div>
                    <div class="phone">
                        <i class="fas fa-phone"></i> {{ $shipment->receiver_phone ?? 'N/A' }}
                        @if($shipment->receiver_postal_code)
                            <br><i class="fas fa-mail-bulk"></i> {{ $shipment->receiver_postal_code }}
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
            <!-- Nepal Flag with Text -->
            <div class="nepal-flag-container">
                <svg class="nepal-flag-svg" viewBox="0 0 120 80" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="0,0 80,0 80,50 0,70" fill="#dc2626"/>
                    <polygon points="2,2 76,2 76,48 2,66" fill="none" stroke="#1e3a8a" stroke-width="3"/>
                    <circle cx="30" cy="25" r="12" fill="white" stroke="#1e3a8a" stroke-width="1.5"/>
                    <circle cx="30" cy="25" r="8" fill="none" stroke="#dc2626" stroke-width="1.5"/>
                    <line x1="30" y1="10" x2="30" y2="15" stroke="#1e3a8a" stroke-width="2"/>
                    <line x1="30" y1="35" x2="30" y2="40" stroke="#1e3a8a" stroke-width="2"/>
                    <line x1="15" y1="25" x2="20" y2="25" stroke="#1e3a8a" stroke-width="2"/>
                    <line x1="40" y1="25" x2="45" y2="25" stroke="#1e3a8a" stroke-width="2"/>
                    <path d="M 50,45 Q 58,40 65,45 Q 58,50 50,45" fill="white" stroke="#1e3a8a" stroke-width="1.5"/>
                    <path d="M 53,44 Q 58,42 62,44 Q 58,46 53,44" fill="#dc2626" stroke="none"/>
                </svg>
                <span class="nepal-flag-text">जय नेपाल</span>
            </div>

            <!-- Header -->
            <div class="hawb-header">
                <div class="logo">NET<span>PACK</span></div>
                <span class="hawb-badge">INTERNATIONAL</span>
            </div>

            <!-- Title -->
            <div class="hawb-title">
                HOUSE AIR WAYBILL
                <div class="sub">International Shipment Document</div>
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
                        <div class="value">{{ strtoupper($shipment->service_type ?? 'Standard') }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Type</div>
                        <div class="value">{{ strtoupper($shipment->shipment_type ?? 'Parcel') }}</div>
                    </div>
                    <div class="info-card">
                        <div class="label">Weight</div>
                        <div class="value">{{ number_format($shipment->chargeable_weight ?? 0, 2) }} kg</div>
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
                        {{ $shipment->sender_city ?? '' }}, {{ $shipment->sender_country ?? '' }}
                    </div>
                    <div class="phone"><i class="fas fa-phone"></i> {{ $shipment->sender_phone ?? 'N/A' }}</div>
                </div>
                <div class="party-box consignee-box">
                    <div class="flag-watermark">🇳🇵</div>
                    <div class="jai-nepal-watermark">जय नेपाल</div>
                    <div class="label consignee">CONSIGNEE / RECEIVER</div>
                    <div class="name">{{ $shipment->receiver_name ?? 'N/A' }}</div>
                    <div class="address">
                        {{ $shipment->receiver_address ?? '' }}<br>
                        {{ $shipment->receiver_city ?? '' }}, {{ $shipment->receiver_country ?? '' }}
                    </div>
                    <div class="phone">
                        <i class="fas fa-phone"></i> {{ $shipment->receiver_phone ?? 'N/A' }}
                        @if($shipment->receiver_postal_code)
                            <br><i class="fas fa-mail-bulk"></i> {{ $shipment->receiver_postal_code }}
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