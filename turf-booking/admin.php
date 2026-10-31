<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }
        .mini { width: 80px; margin: 0; padding: 0.4rem; }
        .link-btn { border: none; background: none; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <nav><h1>⚽ TurfBook Admin</h1><div><a href="index.html">Exit to Site</a></div></nav>
    
    <div class="container admin-grid">
        <!-- LEFT COL: Management Forms (AJAX) -->
        <div>
            <div class="card">
                <h3>Add New Turf</h3>
                <form id="addTurfForm">
                    <input type="text" name="name" placeholder="Turf Name" required>
                    <input type="text" name="loc" placeholder="Location" required>
                    <input type="number" name="pf" placeholder="Football Price/hr" required>
                    <input type="number" name="pc" placeholder="Cricket Price/hr" required>
                    <button type="submit" class="btn" style="width: 100%;">Add Turf</button>
                </form>
            </div>

            <div class="card">
                <h3>Block Slot (Maintenance)</h3>
                <form id="blockSlotForm">
                    <input type="number" name="turf" placeholder="Turf ID (1 or 2)" required>
                    <input type="date" name="date" required>
                    <input type="text" name="slot" placeholder="e.g. 10:00-11:00" required>
                    <button type="submit" class="btn btn-danger" style="width: 100%;">Block Slot</button>
                </form>
            </div>

            <div class="card">
                <h3>Blocked Slots</h3>
                <table>
                    <thead><tr><th>ID</th><th>Turf</th><th>Date</th><th>Slot</th><th></th></tr></thead>
                    <tbody id="blockedList"></tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT COL -->
        <div>
            <div class="card">
                <h3>Manage Turfs</h3>
                <table>
                    <thead><tr><th>ID</th><th>Name</th><th>Football ₹</th><th>Cricket ₹</th><th></th></tr></thead>
                    <tbody id="turfList"></tbody>
                </table>
            </div>

            <div class="card">
                <h3>Live Bookings Dashboard</h3>
                <table>
                    <thead>
                        <tr><th>User</th><th>Turf</th><th>Date/Slot</th><th>Sport</th><th>Amount</th><th>Status</th></tr>
                    </thead>
                    <tbody id="bookingList"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        async function crud(fd) {
            const res = await fetch('api/admin_crud.php', { method: 'POST', body: fd });
            return res.json();
        }

        document.getElementById('addTurfForm').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target); fd.append('action', 'add_turf');
            await crud(fd);
            alert('Turf Added!');
            e.target.reset(); loadTurfs();
        };

        document.getElementById('blockSlotForm').onsubmit = async (e) => {
            e.preventDefault();
            const fd = new FormData(e.target); fd.append('action', 'block_slot');
            await crud(fd);
            alert('Slot Blocked!');
            e.target.reset(); loadBlocked();
        };

        async function loadTurfs() {
            const data = await (await fetch('api/get_turfs.php')).json();
            document.getElementById('turfList').innerHTML = data.map(t => `
                <tr>
                    <td>${t.id}</td><td>${t.name}<br><small>${t.location}</small></td>
                    <td><input class="mini" type="number" id="pf${t.id}" value="${t.price_football}"></td>
                    <td><input class="mini" type="number" id="pc${t.id}" value="${t.price_cricket}"></td>
                    <td>
                        <button class="link-btn" style="color: #3b82f6;" onclick="updateTurf(${t.id})">Save</button>
                        <button class="link-btn" style="color: red;" onclick="deleteTurf(${t.id})">Delete</button>
                    </td>
                </tr>`).join('');
        }

        async function updateTurf(id) {
            const fd = new FormData(); fd.append('action', 'update_turf'); fd.append('id', id);
            fd.append('pf', document.getElementById('pf' + id).value);
            fd.append('pc', document.getElementById('pc' + id).value);
            await crud(fd); alert('Prices updated!');
        }

        async function deleteTurf(id) {
            if (!confirm('Delete this turf?')) return;
            const fd = new FormData(); fd.append('action', 'delete_turf'); fd.append('id', id);
            const r = await crud(fd);
            if (r.status === 'error') alert(r.message);
            loadTurfs();
        }

        async function loadBlocked() {
            const data = await (await fetch('api/get_blocked.php')).json();
            document.getElementById('blockedList').innerHTML = data.map(b => `
                <tr>
                    <td>${b.id}</td><td>${b.turf_id}</td><td>${b.block_date}</td><td>${b.slot_time}</td>
                    <td><button class="link-btn" style="color: red;" onclick="unblock(${b.id})">Unblock</button></td>
                </tr>`).join('');
        }

        async function unblock(id) {
            const fd = new FormData(); fd.append('action', 'unblock_slot'); fd.append('id', id);
            await crud(fd); loadBlocked();
        }

        async function loadBookings() {
            const data = await (await fetch('api/get_all_bookings.php')).json();
            document.getElementById('bookingList').innerHTML = data.map(b => `
                <tr>
                    <td>${b.name}<br><small>${b.phone}</small></td>
                    <td>${b.turf}</td>
                    <td>${b.booking_date}<br>${b.slot_time}</td>
                    <td>${b.sport}</td>
                    <td>₹${b.total_amount}</td>
                    <td style="color: ${b.status==='confirmed'?'green':'red'}; font-weight: bold;">${b.status.toUpperCase()}</td>
                </tr>`).join('');
        }

        loadTurfs(); loadBlocked(); loadBookings();
    </script>
</body>
</html>
