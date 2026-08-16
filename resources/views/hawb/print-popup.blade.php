<!DOCTYPE html>
<html>
<head>
    <title>HAWB - {{ $shipment->tracking_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .hawb-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .hawb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #0d9488;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .hawb-header .logo {
            font-size: 28px;
            font-weight: bold;
            color: #0d9488;
        }
        .hawb-header .logo span {
            color: #1e293b;
        }
        .hawb-badge {
            background: #0d9488;
            color: white;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .info-group {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            background: #f8fafc;
        }
        .info-group .label {
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .info-group .value {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 2px dashed #e2e8f0;
        }
        .qr-section img {
            width: 150px;
            height: 150px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #94a3b8;
        }
        .tracking-number {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            text-align: center;
            padding: 10px;
            background: #f1f5f9;
            border-radius: 8px;
            margin: 10px 0;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="hawb-container">
        <!-- Header -->
        <div class="hawb-header">
            <div class="logo">NET<span>PACK</span></div>
            <div>
                <span class="hawb-badge">{{ strtoupper($type ?? 'INTERNATIONAL') }}</span>
                <span style="margin-left: 10px; font-size: 12px; color: #64748b;">HAWB #{{ $shipment->id }}</span>
            </div>
        </div>

        <h1 style="text-align:center; font-size:24px; font-weight:bold; color:#1e293b; letter-spacing:2px;">
            HOUSE AIR WAYBILL
            <div style="font-size:12px; color:#64748b; font-weight:normal; letter-spacing:0;">Shipment Document</div>
        </h1>

        <!-- Tracking Number -->
        <div class="tracking-number">
            {{ $shipment->tracking_number }}
        </div>

        <!-- QR Code -->
        <div class="qr-section">
            @if(isset($qrCode))
                {!! $qrCode !!}
            @else
                <div style="width:150px;height:150px;background:#e2e8f0;display:inline-block;border-radius:8px;line-height:150px;color:#64748b;">
                    QR Code
                </div>
            @endif
            <p style="font-size:10px; color:#64748b; margin-top:4px;">Scan to track your shipment</p>
        </div>

        <!-- Shipment Details -->
        <div class="info-grid">
            <div class="info-group">
                <div class="label">Service Type</div>
                <div class="value">{{ strtoupper($shipment->service_type ?? 'Standard') }}</div>
            </div>
            <div class="info-group">
                <div class="label">Shipment Type</div>
                <div class="value">{{ strtoupper($shipment->shipment_type ?? 'Parcel') }}</div>
            </div>
            <div class="info-group">
                <div class="label">Weight</div>
                <div class="value">{{ number_format($shipment->chargeable_weight ?? 0, 2) }} kg</div>
            </div>
            <div class="info-group">
                <div class="label">Total Amount</div>
                <div class="value">$ {{ number_format($shipment->total_amount ?? 0, 2) }}</div>
            </div>
            <div class="info-group">
                <div class="label">Status</div>
                <div class="value" style="color: {{ $shipment->status === 'delivered' ? '#10b981' : ($shipment->status === 'pending' ? '#f59e0b' : '#3b82f6') }}">
                    {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                </div>
            </div>
            <div class="info-group">
                <div class="label">HAWB Number</div>
                <div class="value">{{ $shipment->hawb_number ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Sender & Receiver -->
        <div class="info-grid">
            <div class="info-group" style="background:#ecfdf5;">
                <div class="label" style="color:#0d9488;">SHIPPER / SENDER</div>
                <div class="value">{{ $shipment->sender_name ?? 'N/A' }}</div>
                <div style="font-size:12px; color:#475569; margin-top:4px;">
                    {{ $shipment->sender_address ?? '' }}<br>
                    {{ $shipment->sender_city ?? '' }}, {{ $shipment->sender_country ?? '' }}
                </div>
            </div>
            <div class="info-group" style="background:#f0f9ff;">
                <div class="label" style="color:#2563eb;">CONSIGNEE / RECEIVER</div>
                <div class="value">{{ $shipment->receiver_name ?? 'N/A' }}</div>
                <div style="font-size:12px; color:#475569; margin-top:4px;">
                    {{ $shipment->receiver_address ?? '' }}<br>
                    {{ $shipment->receiver_city ?? '' }}, {{ $shipment->receiver_country ?? '' }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>
                <strong>NETPACK Logistics</strong><br>
                Kathmandu, Nepal<br>
                www.netpack.com | +977-9800000000
            </div>
            <div style="text-align:right;">
                <div style="font-weight:600; color:#0f172a;">Terms & Conditions</div>
                <div style="font-size:9px; line-height:1.4;">
                    This document is subject to NETPACK's terms and conditions of carriage.<br>
                    For inquiries, contact: support@netpack.com
                </div>
                <div style="margin-top:4px; font-size:9px; color:#94a3b8;">
                    HAWB Generated: {{ now()->format('M d, Y H:i A') }}
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="no-print" style="text-align:center; margin-top:20px; padding-top:15px; border-top:1px solid #e2e8f0;">
            <button onclick="window.print()" style="background:#0d9488; color:white; border:none; padding:10px 30px; border-radius:8px; font-size:14px; cursor:pointer;">
                🖨 Print HAWB
            </button>
        </div>
    </div>
</body>
</html>