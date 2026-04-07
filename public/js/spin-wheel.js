
        const canvas = document.getElementById("wheelCanvas");
        const ctx = canvas.getContext("2d");
        const spinBtn = document.getElementById("spinBtn");
        const modalOverlay = document.getElementById("modalOverlay");
        const modalBox = document.getElementById("modalBox");
        const prizeDisplay = document.getElementById("prizeDisplay");
		const closeModalBtn = document.getElementById("closeModalBtn");
		const badLuckModalOverlay = document.getElementById("badLuckModalOverlay");
		const badLuckModalBox = document.getElementById("badLuckModalBox");
		const closeBadLuckModalBtn = document.getElementById("closeBadLuckModalBtn");

		let sectors = []; // Empty initially

		async function fetchRewards() {
			try {
				const response = await fetch(rewardsApiUrl);
				const data = await response.json();

				if (!response.ok) throw new Error(data.error || "Failed to fetch rewards");

				// Map API rewards to sectors format
				sectors = data.map((r, index) => ({
					id: r.id,
					label: r.label,
					cat: r.cat || "Prize",
					// Fix: Alternating colors using math instead of a separate function
					color: r.color || (index % 2 === 0 ? '#d4af37' : '#B30C12'), 
					chance: parseFloat(r.chance),
					stock: parseInt(r.stock),
					// Fix: Pull the stats directly from the database!
					times_won: parseInt(r.times_won || 0) 
				}));

				// Initialize stats
				spinStats.counts = {};
				sectors.forEach(s => spinStats.counts[s.label] = 0);

				renderInventory();
				drawWheel();
				updateStatsUI();

			} catch (err) {
				console.error("Error fetching rewards:", err);
			}
		}

		// Call this on page load
		fetchRewards();

		function assignColorsToSectors(sectors) {
			const colors = ['#d4af37', '#e61919'];
			sectors.forEach((sector, index) => {
				sector.color = colors[index % colors.length];
			});
		}

		

		const bulbRing = document.getElementById("bulbRing");
		const bulbCount = 40;
		let bulbState = false;
		let currentBulb = 0;

		let spinStats = {
			total: 0,
			counts: {}
		};

		sectors.forEach(s => {
			spinStats.counts[s.label] = 0;
		});

		function renderInventory() {
			const container = document.getElementById("inventoryList");
			container.innerHTML = "";

			sectors.forEach((s, i) => {

				if (typeof s.stock === "undefined") s.stock = 0;

				const row = document.createElement("div");
				row.className = "inventory-row";

				const statusClass = s.stock > 0 ? "in-stock" : "out-stock";

				// LEFT: label + type
				const left = document.createElement("div");
				left.style.display = "flex";
				left.style.flexDirection = "column";

				const label = document.createElement("input");
				label.value = s.label;
				label.style.width = "120px";

				label.addEventListener("input", () => {
					const oldLabel = s.label;
					s.label = label.value;

					// update stats key safely
					spinStats.counts[s.label] = spinStats.counts[oldLabel] || 0;
					delete spinStats.counts[oldLabel];

					updateStatsUI();
					drawWheel();
				});

				const cat = document.createElement("input");
				cat.value = s.cat;
				cat.style.width = "120px";

				cat.addEventListener("input", () => {
					s.cat = cat.value;
				});

				left.appendChild(label);
				left.appendChild(cat);

				// RIGHT SIDE
				const right = document.createElement("div");
				right.style.display = "flex";
				right.style.alignItems = "center";
				right.style.gap = "6px";

				// chance
				const chance = document.createElement("input");
				chance.type = "number";
				chance.value = s.chance;

				chance.addEventListener("input", (e) => {
					s.chance = parseFloat(e.target.value) || 0;
					normalizeChances();
					updateStatsUI();
				});

				// stock
				const stock = document.createElement("input");
				stock.type = "number";
				stock.value = s.stock;

				stock.addEventListener("input", (e) => {
					s.stock = Math.max(0, parseInt(e.target.value) || 0);
					renderInventory();
				});

				// status
				const status = document.createElement("span");
				status.className = "stock-status " + statusClass;
				status.textContent = s.stock > 0 ? "IN" : "OUT";

				// delete
				const del = document.createElement("button");
				del.textContent = "❌";

				del.addEventListener("click", () => {
					delete spinStats.counts[s.label];
					sectors.splice(i, 1);

					normalizeChances();
					renderInventory();
					drawWheel();
					updateStatsUI();
				});

				right.appendChild(chance);
				right.appendChild(stock);
				right.appendChild(status);
				right.appendChild(del);

				row.appendChild(left);
				row.appendChild(right);

				container.appendChild(row);
			});
		}


	function updateStatsUI() {
		// Calculate total based on the DB values
		const totalSpins = sectors.reduce((sum, s) => sum + (s.times_won || 0), 0);
		document.getElementById("totalSpins").innerText = `Total Spins: ${totalSpins}`;
		
		const container = document.getElementById("statsList");
		container.innerHTML = "";

		sectors.forEach(s => {
			const count = s.times_won || 0;
			const percent = totalSpins ? ((count / totalSpins) * 100).toFixed(1) : 0;
			
			container.innerHTML += `
				<div class="stat-row">
					<div style="display:flex; justify-content:space-between">
						<span>${s.label}</span>
						<span style="color:#aaa">${count} hits</span>
					</div>
					<div style="font-size:11px; color:#ffcc00">${percent}% (Target: ${s.chance}%)</div>
					<div class="stat-bar" style="width:${percent}%"></div>
				</div>`;
		});
	}

	function createBulbs() {
		const bulbRing = document.querySelector('.bulb-ring');
		const totalBulbs = 24; 

		for (let i = 0; i < totalBulbs; i++) {
			let bulb = document.createElement('div');
			bulb.classList.add('bulb');
			
			// Calculate angle
			let angle = (i * (360 / totalBulbs)) * (Math.PI / 180);
			
			// Position using percentages so it scales perfectly on mobile
			// 50% is the center. We use 50% radius to push them to the edge.
			let x = 50 + (50 * Math.cos(angle)); 
			let y = 50 + (50 * Math.sin(angle));
			
			bulb.style.left = `calc(${x}% - 6px)`; // 6px is half the bulb width to center the bulb itself
			bulb.style.top = `calc(${y}% - 6px)`;
			
			bulbRing.appendChild(bulb);
		}
	}

	function enterFullscreen() {
		const elem = document.documentElement;

		if (elem.requestFullscreen) {
			elem.requestFullscreen();
		} else if (elem.webkitRequestFullscreen) {
			elem.webkitRequestFullscreen();
		} else if (elem.msRequestFullscreen) {
			elem.msRequestFullscreen();
		}
	}

	function animateBulbs() {
		const bulbs = document.querySelectorAll(".bulb");

		bulbs.forEach((bulb, i) => {
			const diff = (i - currentBulb + bulbs.length) % bulbs.length;

			if (diff === 0) {
				bulb.classList.remove("off");
				bulb.style.opacity = "1";
			} else if (diff === 1) {
				bulb.classList.remove("off");
				bulb.style.opacity = "0.6";
			} else if (diff === 2) {
				bulb.classList.remove("off");
				bulb.style.opacity = "0.3";
			} else {
				bulb.classList.add("off");
				bulb.style.opacity = "0.1";
			}
		});

		currentBulb = (currentBulb + 1) % bulbs.length;
	}

	createBulbs();
	setInterval(animateBulbs, 70);

        const numSectors = sectors.length;
        const arc = (2 * Math.PI) / numSectors;
        
        let currentRotation = 0;
        let idleInterval;
        let isSpinning = false;

        function drawWheel() {
			const ctx = canvas.getContext("2d");
			const radius = canvas.width / 2;
			const arc = (2 * Math.PI) / sectors.length;

			ctx.clearRect(0, 0, canvas.width, canvas.height);

			sectors.forEach((sector, i) => {
				const angle = i * arc;

				// 🎨 Draw slice
				ctx.beginPath();
				ctx.moveTo(radius, radius);
				ctx.arc(radius, radius, radius, angle, angle + arc);
				ctx.fillStyle = sector.color || assignColorsToSectors(sectors);
				ctx.fill();
				ctx.stroke();

				// 🔤 Draw text
				ctx.save(); // IMPORTANT

				ctx.translate(radius, radius);
				ctx.rotate(angle + arc / 2);

				ctx.textAlign = "right";
				ctx.fillStyle = "#fff";
				ctx.font = "bold 14px Arial";

				// ✂️ Trim long text
				let label = sector.label;
				if (label.length > 10) {
					label = label.substring(0, 10) + "...";
				}

				ctx.fillText(formatLabel(sector.label), radius - 10, 5);
				ctx.restore(); // IMPORTANT
			});
		}

		function formatLabel(text) {
			if (text.length > 20) {
				return text.slice(0, 8) + "...";
			}
			return text;
		}

        // --- Idle Animation Logic ---
        function startIdleAnimation() {
            if (isSpinning) return;
            canvas.style.transition = "none"; // Animation မရှိဘဲ ဖြည်းဖြည်းချင်း လည်စေရန်
           	idleInterval = setInterval(() => {
				currentRotation += 0.5;
				canvas.style.transform = `rotate(${currentRotation % 360}deg)`;
			}, 20);
        }

        function stopIdleAnimation() {
            clearInterval(idleInterval);
        }

        drawWheel();
        startIdleAnimation(); // စဖွင့်ချင်း လည်နေစေရန်

        function shootConfetti() {
            const duration = 3000;
            const animationEnd = Date.now() + duration;
            const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 1100 };
            const interval = setInterval(() => {
                const timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) return clearInterval(interval);
                const particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: Math.random() * 0.2 + 0.1, y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: Math.random() * 0.2 + 0.7, y: Math.random() - 0.2 } }));
            }, 250);
        }

        spinBtn.addEventListener("click", async () => {
			if (isSpinning) return;
			isSpinning = true;
			spinBtn.disabled = true;
			stopIdleAnimation();
			clearInterval(idleInterval);

			// Optional: fullscreen for better UX
			enterFullscreen();

			try {
				// 1️⃣ Request server for winner
				const response = await fetch(spinApiUrl, {
					method: "POST",
					headers: { "Content-Type": "application/json" },
					body: JSON.stringify({}) // send user/session info if needed
				});
				const data = await response.json();
				if (!response.ok) throw new Error(data.error || "Server error");

				// 2️⃣ Find winner in local sectors array
				const winnerIndex = sectors.findIndex(s => s.id === data.reward_id);
				if (winnerIndex === -1) throw new Error("Winner mismatch");

				const win = sectors[winnerIndex];

				// 3️⃣ Local stock safety check
				if (!win || win.stock <= 0) {
					alert("Selected reward is out of stock!");
					throw new Error("Out of stock");
				}
				win.stock--; // Deduct stock locally

				// 4️⃣ Compute rotation
				const sectorDeg = 360 / sectors.length;
				const targetDeg = 270 - (winnerIndex * sectorDeg) - (sectorDeg / 2);
				const extraSpins = 2160; // 6 full rotations
				currentRotation = (currentRotation - (currentRotation % 360)) + extraSpins + (targetDeg < 0 ? targetDeg + 360 : targetDeg);

				// 5️⃣ Animate the wheel
				canvas.style.transition = "transform 5s cubic-bezier(0.15, 0, 0.15, 1)";
				canvas.style.transform = "rotate(" + currentRotation + "deg)";

				// 6️⃣ Show result after animation
				setTimeout(async() => {
					
			
					const isBadLuck = win.label.toLowerCase().includes('bad luck') || 
                                      win.label.toLowerCase().includes('try again') ||
                                      win.label === 'Thank You'; 

                    if (isBadLuck) {
                        showBadLuckModal();
                        // Notice: NO confetti here!
                    } else {
                        showModal(win.label);
                        shootConfetti();
                    }
                    
                    await fetchRewards();

					// showModal(data.reward); // Display reward
					// confetti({ particleCount: 200, spread: 360, origin: { y: 0.5 } }); // Smooth confetti
					// fetchRewards(); // Optional: sync stock from server
					// console.log("Fetch rewards");
					// startIdleAnimation();
					
				}, 5000);

			} catch (err) {
				alert("Spin failed: " + err.message);
				console.error(err);
				isSpinning = false;
				spinBtn.disabled = false;
				startIdleAnimation();
			}
		});

        function showModal(prize) {
            prizeDisplay.innerText = prize;
            modalOverlay.style.display = 'flex';
            setTimeout(() => { modalBox.classList.add('active'); }, 10);
        }

        function closeModal() {
            modalBox.classList.remove('active');
            setTimeout(() => {
                modalOverlay.style.display = 'none';
                isSpinning = false;
                spinBtn.disabled = false;
                startIdleAnimation(); // Modal ပိတ်ရင် Idle ပြန်စမယ်
            }, 300);
        }

		function showBadLuckModal() {
            badLuckModalOverlay.style.display = 'flex';
            setTimeout(() => { badLuckModalBox.classList.add('active'); }, 10);
        }

        // Updated to close BOTH types of modals safely
        function closeModal() {
            if (modalBox) modalBox.classList.remove('active');
            if (badLuckModalBox) badLuckModalBox.classList.remove('active');
            
            setTimeout(() => {
                if (modalOverlay) modalOverlay.style.display = 'none';
                if (badLuckModalOverlay) badLuckModalOverlay.style.display = 'none';
                
                isSpinning = false;
                spinBtn.disabled = false;
                startIdleAnimation(); 
            }, 300);
        }

        // Ensure both buttons trigger the close function
        if (closeModalBtn) {
            closeModalBtn.addEventListener("click", closeModal);
        }
        if (closeBadLuckModalBtn) {
            closeBadLuckModalBtn.addEventListener("click", closeModal);
        }

		function pickWinnerIndex() {
			const available = sectors.filter(s => s.stock > 0 && s.chance > 0);

			if (available.length === 0) {
				console.error("No available rewards!");
				return -1;
			}

			const total = available.reduce((sum, s) => sum + s.chance, 0);
			const rand = Math.random() * total;

			let cumulative = 0;

			for (let i = 0; i < available.length; i++) {
				cumulative += available[i].chance;
				if (rand < cumulative) {
					return sectors.indexOf(available[i]);
				}
			}

			return sectors.indexOf(available[0]);
		}

		function normalizeChances() {
			const total = sectors.reduce((sum, s) => sum + s.chance, 0);

			if (total === 0) return;

			sectors.forEach(s => {
				s.chance = (s.chance / total) * 100;

				if (s.chance < 0.5) s.chance = 0.5;

			});
		}

		closeModalBtn.addEventListener("click", async () => {
			closeModal();
		});

		// --- Panel Toggle Logic ---
		document.addEventListener("DOMContentLoaded", () => {
			const inquiryBtn = document.getElementById("inquiryBtn");
			const statsPanel = document.getElementById("statsPanel");

			if (inquiryBtn && statsPanel) {
				inquiryBtn.addEventListener("click", () => {
					// Toggles the visibility of the panel
					statsPanel.classList.toggle("show");
					
					// Optional: Change button text when open
					if (statsPanel.classList.contains("show")) {
						inquiryBtn.innerText = "✖ Close";
					} else {
						inquiryBtn.innerText = "📊 Inquiry";
					}
				});
			}
		});
    