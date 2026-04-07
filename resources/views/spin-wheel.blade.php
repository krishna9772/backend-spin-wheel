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
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            height: 100vh; margin: 0; 
            background: url("{{asset('images/background.jpg')}}") no-repeat center center fixed;
            background-size: cover;
            font-family: 'Pyidaungsu', sans-serif; color: white; overflow: hidden;
            position: relative;
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
        @media (max-width: 600px) {
            #statsPanel {
                top: 70px;
                right: 10px;
                left: 10px; /* Stretches across the screen on mobile */
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <button id="inquiryBtn" class="inquiry-btn">📊 Inquiry</button>

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