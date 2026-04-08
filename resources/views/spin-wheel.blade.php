<!DOCTYPE html>
<html lang="my">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lucky Splash Wheel</title>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <link href="{{ asset('css/spin-wheel.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset("images/favicon.ico")}}" type="image/x-icon">

    <style>
    
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            min-height: 100vh; /* better than height */
            margin: 0;

            background-image: url("{{asset('images/background.jpg')}}");
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;

            font-family: 'Pyidaungsu', sans-serif;
            color: white;
            overflow-x: hidden;
            position: relative;
        }

        /* --- Modal Themes --- */
        /* Default / Bad Luck */
        .theme-sad {
            background-color: #2a2a2a;
            color: #e0e0e0;
            border-top: 6px solid #B30C12; /* Red top border */
        }

        /* Have a Good Day */
        .theme-good-day {
            background-color: #fff9e6;
            color: #d4af37; /* Gold text */
            border-top: 6px solid #d4af37;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
        }

        /* Happy Thingyan */
        .theme-thingyan {
            background-color: #e0f7fa; /* Light cyan background */
            color: #00838f; /* Deep water blue */
            border-top: 6px solid #00bcd4;
            position: relative;
            overflow: hidden; /* Keeps water inside the box */
        }

        /* --- Water Drop Animation --- */
        #waterAnimationContainer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Let clicks pass through */
            z-index: 0;
        }

        .water-drop {
            position: absolute;
            bottom: 100%;
            width: 8px;
            height: 16px;
            background: rgba(0, 188, 212, 0.6);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: fall linear forwards;
        }

        @keyframes fall {
            to {
                transform: translateY(300px); /* Adjust based on your modal height */
                opacity: 0;
            }
        }

        /* Ensure text stays above the water */
        #badLuckTitle, #badLuckDesc, #closeBadLuckModalBtn {
            position: relative;
            z-index: 1;
        }

        /* 1. Style the floating Inquiry Button */
        .inquiry-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #440000;
            background: linear-gradient(#ffea00, #ff9500);
            border: none;
            border-radius: 25px;
            cursor: pointer;
            box-shadow: 0 4px 0 #b36b00;
            z-index: 1000;
            transition: 0.2s;
            font-family: 'Pyidaungsu', sans-serif;
        }

        .inquiry-btn:active {
            transform: translateY(4px);
            box-shadow: 0 0 0 #b36b00;
        }

        /* 2. Hide the stats panel by default and position it under the button */
        #statsPanel {
            position: absolute;
            top: 70px; /* Sits right below the button */
            right: 20px;
            display: none; /* Hidden by default */
            z-index: 999;
            /* Optional: Add a smooth fade-in */
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        /* 3. The class that JavaScript will toggle */
        #statsPanel.show {
            display: flex;
            opacity: 1;
        }

        /* Mobile tweak to ensure it fits on small screens */
        /* Mobile tweak to ensure it fits on small screens */
        @media (max-width: 600px) {
            /* Hide the inquiry button on mobile */
            .inquiry-btn {
                display: none;
            }

            #statsPanel {
                top: 70px;
                right: 10px;
                left: 10px; 
                max-width: none;
            }
        }

        @media (max-width: 768px) {
            body {
                background-position: center top;
                background-size: cover;
            }
        }
    </style>
</head>
<body>
    @auth
        <button id="inquiryBtn" class="inquiry-btn">📊 Inquiry</button>
    @endauth

    <h1> Lucky Splash Wheel </h1>

    <div class="wheel-container">
        <div class="pointer"></div>
        <div class="outer-glow">
            <canvas id="wheelCanvas" width="400" height="400"></canvas>
            <div class="bulb-ring" id="bulbRing"></div>
            <div class="center-logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo">
            </div>
        </div>
    </div>

    <button class="spin-btn" id="spinBtn">SPIN NOW</button>

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal" id="modalBox">
            <h2>🎉 ဂုဏ်ယူပါတယ်! 🎉</h2>
            <p>သင်ရရှိတဲ့ဆုကတော့: <br>
               <span class="prize-text" id="prizeDisplay"></span>
            </p>
            <button class="close-btn" id="closeModalBtn"> Thank You </button>
        </div>
    </div>

    <div id="badLuckModalOverlay" class="modal-overlay" style="display: none;">
        <div id="badLuckModalBox" class="modal">
            <div id="waterAnimationContainer"></div> 
            
            <h2 id="badLuckTitle">Oh no...</h2>
            <p id="badLuckDesc" style="margin-top: 10px; font-size: 18px;">Try again next time.</p>
            
            <button id="closeBadLuckModalBtn" class="close-btn">Close</button>
        </div>
    </div>

    <div class="side-panels" id="statsPanel">
        <div class="panel" style="display: none !important;">
            {{-- <div style="margin-bottom:10px;">
                <input id="newLabel" placeholder="Item" style="width:100%; margin-bottom:5px;">
                <input id="newCat" placeholder="Type" style="width:100%; margin-bottom:5px;">
                <input id="newChance" type="number" placeholder="Chance %" style="width:48%;">
                <input id="newStock" type="number" placeholder="Qty" style="width:48%;">
                <button id="addBtn" style="width:100%; margin-top:5px;">➕ Add Item</button>
            </div>
            <h3>📦 Gift Inventory (Set Qty)</h3> --}}
            <div id="inventoryList"></div>
        </div>

        <div class="panel">
            <h3>📊 Live Statistics</h3>
            <div id="totalSpins" style="margin-bottom:10px; font-weight:bold; color: #ffcc00;">Total Spins: 0</div>
            <div id="statsList"></div>
        </div>

        {{-- <button id="runTestBtn" data-spins="10000" data-mode="random">Run Test 10000</button> --}}
    </div>

    <script>
        const spinApiUrl = "{{ route('spin.api') }}"; // Laravel route for API
        const rewardsApiUrl = "api/admin/rewards";

    </script>
    <script src="{{ asset('js/spin-wheel.js') }}"></script>
</body>
</html>