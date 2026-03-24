<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinical Referral Slip - {{ $referralSlip->patient->full_name }}</title>
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 19cm;
            margin: 0 auto;
            border: 1px solid #eee;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: 80px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            font-weight: normal;
        }
        .header .title {
            font-size: 20px;
            margin-top: 15px;
            border-bottom: 2px solid #000;
            display: inline-block;
            padding-bottom: 5px;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        .field {
            margin-bottom: 8px;
            border-bottom: 1px dotted #000;
            display: flex;
            align-items: flex-end;
        }
        .label {
            font-weight: bold;
            text-transform: uppercase;
            margin-right: 10px;
            white-space: nowrap;
        }
        .value {
            flex-grow: 1;
            padding-bottom: 2px;
        }
        .routing-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .routing-box {
            border: 1px solid #000;
            padding: 10px;
        }
        .routing-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .box {
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            display: inline-block;
            text-align: center;
            line-height: 14px;
            font-size: 10px;
            font-weight: bold;
        }
        .content-section {
            margin-bottom: 15px;
        }
        .content-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
            border-bottom: 1px solid #000;
        }
        .content-body {
            min-height: 60px;
            padding: 5px 0;
            white-space: pre-wrap;
        }
        .signatures {
            margin-top: 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        .sig-box {
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .sig-label {
            font-size: 10px;
            color: #666;
        }
        @media print {
            .no-print {
                display: none;
            }
            .container {
                border: none;
            }
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            z-index: 100;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">PRINT SLIP</button>

    <div class="container">
        <div class="header">
            {{-- Barangay Logo --}}
            <div class="logo">
                <img src="{{ asset('assets/images/LOGO (2).png') }}" alt="Logo" style="width: 100%; height: auto; display: block;">
            </div>
            <h1>E-Barangay Health</h1>
            <h2>Carajay, Lapu-Lapu City / Tel. No. 341-3681</h2>
            <div class="title">CLINICAL REFERRAL SLIP</div>
        </div>

        <div class="info-grid">
            <div class="left-col">
                <div class="field">
                    <span class="label">Name:</span>
                    <span class="value">{{ $referralSlip->patient->full_name }}</span>
                </div>
                <div class="field">
                    <span class="label">Address:</span>
                    <span class="value">{{ $referralSlip->patient->address }}, {{ $referralSlip->patient->purok }}</span>
                </div>
            </div>
            <div class="right-col">
                <div class="field">
                    <span class="label">Date:</span>
                    <span class="value">{{ $referralSlip->date->format('m-d-Y') }}</span>
                </div>
                <div class="field">
                    <span class="label">Age:</span>
                    <span class="value">{{ $referralSlip->patient->age }}</span>
                </div>
                <div class="field">
                    <span class="label">Family No:</span>
                    <span class="value">{{ $referralSlip->family_no ?? '_________' }}</span>
                </div>
            </div>
        </div>

        <div class="routing-section">
            <div class="routing-box">
                <span class="routing-title">Referred From:</span>
                <div class="checkbox-group">
                    @foreach(['RHM', 'PHN', 'PHD', 'SI', 'CHO'] as $type)
                        <div class="checkbox-item">
                            <div class="box">{{ in_array($type, $referralSlip->referred_from) ? 'X' : '' }}</div>
                            <span>{{ $type }}</span>
                        </div>
                    @endforeach
                    <div class="checkbox-item">
                        <div class="box">{{ in_array('Others', $referralSlip->referred_from) ? 'X' : '' }}</div>
                        <span>OTHERS: {{ $referralSlip->referred_from_other }}</span>
                    </div>
                </div>
            </div>
            <div class="routing-box">
                <span class="routing-title">Referred To:</span>
                <div class="checkbox-group">
                    @foreach(['RHM', 'PHN', 'PHD', 'SI', 'CHO'] as $type)
                        <div class="checkbox-item">
                            <div class="box">{{ in_array($type, $referralSlip->referred_to) ? 'X' : '' }}</div>
                            <span>{{ $type }}</span>
                        </div>
                    @endforeach
                    <div class="checkbox-item">
                        <div class="box">{{ in_array('Others', $referralSlip->referred_to) ? 'X' : '' }}</div>
                        <span>OTHERS: {{ $referralSlip->referred_to_other }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-section">
            <span class="content-title">Pertinent Findings by Referring Level:</span>
            <div class="content-body">{{ $referralSlip->pertinent_findings }}</div>
        </div>

        <div class="content-section">
            <span class="content-title">Reason for Referral:</span>
            <div class="content-body">{{ $referralSlip->reason_for_referral }}</div>
        </div>

        <div class="content-section">
            <span class="content-title">Instruction to Referring Level:</span>
            <div class="content-body">{{ $referralSlip->instruction_to_referring_level }}</div>
        </div>

        <div class="content-section">
            <span class="content-title">Actions Taken by Referred Level:</span>
            <div class="content-body">{{ $referralSlip->actions_taken_by_referred_level }}</div>
        </div>

        <div class="content-section">
            <span class="content-title">Instructions to Referring Level:</span>
            <div class="content-body">{{ $referralSlip->instructions_to_referring_level_final }}</div>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line">Referring Level</div>
                <div class="sig-label">(Signature over Printed Name)</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Referred Level</div>
                <div class="sig-label">(Signature over Printed Name)</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print on load if not in preview mode
        // window.onload = () => window.print();
    </script>
</body>
</html>
