<!DOCTYPE html>
<html>
<head>
    <title>Admin Activity Log</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        nav {
            width: 100%;
            background-color: #343a40;
            padding: 10px;
            box-sizing: border-box;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
            position: relative; /* For dropdown positioning */
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 20px;
        }
        nav ul li:hover ul {
            display: block;
        }
        nav ul ul {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #343a40;
            padding: 10px;
            list-style: none;
            margin: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        nav ul ul li {
            margin: 0;
            padding: 5px 0;
        }
        nav ul ul li a {
            font-size: 16px;
        }
        h1 {
            color: #343a40;
        }
        .log-section {
            width: 100%;
            max-width: 60%;
            margin-top: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 40px;
        }
        .transaction-item {
            border-bottom: 1px solid #dee2e6;
            padding: 10px 0;
            color: #495057;
        }
        .transaction-item:last-child {
            border-bottom: none;
        }
        .filter-section {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
            gap: 10px; /* Added gap for closer alignment */
            flex-wrap: wrap;
        }
        .filter-section select, .filter-section input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-right: 0; /* Removed margin-right to bring filters closer */
        }
    </style>
</head>
<body>
    <nav>
        <ul>         
            <li><a href="admin.php">Admin Home</a></li>
            <li><a href="add.php">Add</a></li>
            <li><a href="edit.php">Edit</a></li>
            <li><a href="delete.php">Delete</a></li>
            <li>
                <a href="#">Activity Log</a> <!-- Dropdown Trigger -->
                <ul>
                    <li><a href="adminLog.php">Admin Log</a></li>
                    <li><a href="userLog.php">User Log</a></li>
                </ul>
            </li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>

    <h1>Admin Activity Log</h1>

    <div class="log-section">
        <div class="filter-section">
            <select id="actionFilter">
                <option value="">All Actions</option>
                <option value="Add">Add</option>
                <option value="Edit">Edit</option>
                <option value="Delete">Delete</option>
            </select>
            <input type="date" id="startDateFilter" placeholder="Start Date" />
            <input type="date" id="endDateFilter" placeholder="End Date" />
        </div>
        <div id="adminLog"></div>
    </div>

    <script>
        function displayAdminLog() {
            const adminActions = JSON.parse(localStorage.getItem('adminActions')) || [];
            const adminLogDiv = document.getElementById('adminLog');
            adminLogDiv.innerHTML = '';

            const actionFilter = document.getElementById('actionFilter').value;
            const startDateFilter = document.getElementById('startDateFilter').value;
            const endDateFilter = document.getElementById('endDateFilter').value;

            const filteredActions = adminActions.filter(action => {
                let matchesAction = true;
                let matchesDateRange = true;

                if (actionFilter && action.type !== actionFilter) {
                    matchesAction = false;
                }

                if (startDateFilter || endDateFilter) {
                    const actionDate = new Date(action.date);
                    const startDate = startDateFilter ? new Date(startDateFilter) : new Date('1970-01-01');
                    const endDate = endDateFilter ? new Date(endDateFilter) : new Date();

                    if (actionDate < startDate || actionDate > endDate) {
                        matchesDateRange = false;
                    }
                }

                return matchesAction && matchesDateRange;
            });

            if (filteredActions.length === 0) {
                adminLogDiv.innerHTML = '<p>No admin actions logged yet.</p>';
            } else {
                filteredActions.forEach(action => {
                    const actionDiv = document.createElement('div');
                    actionDiv.className = 'transaction-item';
                    actionDiv.innerHTML = `
                        <strong>Date:</strong> ${action.date} <br>
                        <strong>Action:</strong> ${action.type} <br>
                        ${action.details}
                    `;
                    adminLogDiv.appendChild(actionDiv);
                });
            }
        }

        document.getElementById('actionFilter').addEventListener('change', displayAdminLog);
        document.getElementById('startDateFilter').addEventListener('change', displayAdminLog);
        document.getElementById('endDateFilter').addEventListener('change', displayAdminLog);

        displayAdminLog();
    </script>
</body>
</html>