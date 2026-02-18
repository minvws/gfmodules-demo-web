@extends('layouts.app')

@section('sidebar')
    <div class="sidebar">
        <p>Informatie</p>

        <ul>
            <li>Invoer gegevens</li>
            <li>Aanmaken pseudoniem</li>
            <li>Verzending naar de PRS</li>
            <li>Verzending naar de NVI</li>
        </ul>
    </div>
@endsection

@section('content')
    <div class="content-wrapper">
        {{-- Top info card --}}
        <div class="info-card">
            <h2 class="info-card-title">Huisarts bloedspoed</h2>

            <div class="info-row">
                <span class="info-label">Patient</span>
                <span class="info-value">{{ $patient ?? 'BSN 123456788' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Zorgcontext</span>
                <span class="info-value">{{ $zorgcontext ?? 'xxx' }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">Organisatietype</span>
                <span class="info-value">{{ $organisatietype ?? 'Org 1' }}</span>
            </div>
        </div>

        {{-- Animation Area --}}
        <div class="animation-area">
            {{-- JWE Envelope --}}
            <div class="jwe-envelope" id="jweEnvelope">
                <div class="envelope-content">
                    <p><strong>ENCRYPTED DATA FOR NVI</strong></p>
                </div>
                <div class="envelope-lock">
                    <div class="lock-icon">
                        <div class="lock-body">JWE</div>
                        {{--                        <div class="lock-shackle"></div>--}}
                    </div>
                </div>
                <div class="envelope-flap"></div>
            </div>

            {{-- Arrow (shows during animation) --}}
            <div class="flow-arrow" id="flowArrow">
                <div class="arrow-line"></div>
            </div>

            {{-- NVI Postbox --}}
            <div class="nvi-postbox" id="nviPostbox">
                <span class="nvi-label">NVI</span>
                <div class="postbox-slots">
                    <div class="postbox-slot" id="slot1"></div>
                    <div class="postbox-slot" id="slot2"></div>
                </div>
            </div>
        </div>

        {{-- Bottom button --}}
        <div class="button-wrapper">
            <button type="button" class="btn btn-secondary" id="sendBtn">Volgende stap</button>
        </div>
    </div>

    <style>
        .content-wrapper {
            padding: 40px 60px;
        }

        /* Info Card */
        .info-card {
            border: 1px solid #999;
            padding: 20px 30px;
            max-width: 350px;
            margin-bottom: 30px;
        }

        .info-card-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            width: 120px;
            color: #333;
        }

        .info-value {
            font-weight: 500;
        }

        /* Sidebar Links */
        .sidebar-link {
            display: block;
            color: #0066cc;
            text-decoration: none;
            font-size: 13px;
            margin-top: 15px;
        }

        .sidebar-link:hover {
            text-decoration: underline;
        }

        /* Animation Area */
        .animation-area {
            position: relative;
            height: 200px;
            margin: 40px 0;
            display: flex;
            align-items: center;
            gap: 40px;
        }

        /* JWE Envelope */
        .jwe-envelope {
            position: relative;
            width: 180px;
            height: 120px;
            background: linear-gradient(145deg, #5a8fe4 0%, #4a7fd4 100%);
            border-radius: 4px;
            padding: 20px;
            transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 10;
        }

        .jwe-envelope.fly {
            transform: translateX(320px) scale(0.15);
            opacity: 0;
        }

        .envelope-flap {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 0;
            border-style: solid;
            border-width: 35px 90px 0 90px;
            border-color: #6a9fee transparent transparent transparent;
        }

        .envelope-content {
            margin-top: 25px;
            color: #fff;
            font-size: 12px;
            font-style: italic;
        }

        .envelope-content span {
            color: #ffd700;
        }

        .envelope-content p {
            margin-bottom: 4px;
        }

        .envelope-lock {
            position: absolute;
            bottom: 12px;
            right: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .lock-shackle {
            width: 20px;
            height: 12px;
            border: 3px solid #ffd700;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }

        .lock-body {
            background-color: #ffd700;
            color: #333;
            font-size: 9px;
            font-weight: bold;
            padding: 3px 6px;
            border-radius: 2px;
        }

        /* Flow Arrow */
        .flow-arrow {
            display: flex;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .flow-arrow.visible {
            opacity: 1;
        }

        .arrow-line {
            width: 80px;
            height: 2px;
            background-color: #333;
            position: relative;
        }

        .arrow-line::after {
            content: '';
            position: absolute;
            right: 0;
            top: -4px;
            border-style: solid;
            border-width: 5px 0 5px 8px;
            border-color: transparent transparent transparent #333;
        }

        /* NVI Postbox */
        .nvi-postbox {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .nvi-label {
            font-weight: bold;
            font-size: 16px;
        }

        .postbox-slots {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .postbox-slot {
            width: 70px;
            height: 35px;
            border: 2px solid #333;
            border-radius: 20px;
            background-color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .postbox-slot::after {
            content: '';
            width: 25px;
            height: 3px;
            background-color: #333;
            border-radius: 2px;
        }

        /* Envelope inside slot */
        .postbox-slot.received::before {
            content: '';
            position: absolute;
            width: 25px;
            height: 18px;
            background: linear-gradient(145deg, #5a8fe4 0%, #4a7fd4 100%);
            border-radius: 2px;
            animation: dropIn 0.4s ease-out forwards;
        }

        @keyframes dropIn {
            0% {
                transform: translateY(-30px) scale(0.5);
                opacity: 0;
            }
            60% {
                transform: translateY(2px);
                opacity: 1;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Button */
        .button-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-secondary {
            padding: 12px 40px;
            font-size: 14px;
            background-color: #fff;
            border: 1px solid #999;
            cursor: pointer;
            min-width: 160px;
            transition: background-color 0.2s;
        }

        .btn-secondary:hover {
            background-color: #f5f5f5;
        }

        .btn-secondary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

    <script>
        // Preload the sound
        const whooshSound = new Audio('/lordsonny-whoosh-cinematic-161021.mp3');
        whooshSound.volume = 0.5;

        document.getElementById('sendBtn').addEventListener('click', function() {
            const btn = this;
            const envelope = document.getElementById('jweEnvelope');
            const arrow = document.getElementById('flowArrow');
            const slot1 = document.getElementById('slot1');

            btn.disabled = true;

            // Step 1: Show arrow
            arrow.classList.add('visible');

            // Step 2: Play whoosh and fly envelope to postbox
            setTimeout(() => {
                whooshSound.play();
                envelope.classList.add('fly');
            }, 300);

            // Step 3: Show envelope in slot
            setTimeout(() => {
                slot1.classList.add('received');
            }, 1000);

            // Step 4: Redirect to next page
            setTimeout(() => {
                window.location.href = "{{ route('step_4') }}";
            }, 1800);
        });
    </script>
@endsection


@section('explanation')
    <div class="explanation">
        <p>
            In this step we will send the encrypted package to the NVI.
        </p>
    </div>
@endsection
