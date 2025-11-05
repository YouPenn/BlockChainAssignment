<!DOCTYPE html>
<html>
<head>
    <title>User Activity Log</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
            position: relative;
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
            position: relative;
        }
        .transaction-item {
            border-bottom: 1px solid #dee2e6;
            padding: 10px 0;
            color: #495057;
        }
        .transaction-item:last-child {
            border-bottom: none;
        }
        .total-amount, .total-fee {
            font-weight: bold;
            color: #007bff;
            margin-top: 20px;
            text-align: right;
        }
        .clear-log-button {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .clear-log-button:hover {
            background-color: #c82333;
        }
        .filter-section {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-section input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-right: 0;
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

    <h1>User Activity Log</h1>

    <!-- User Purchase History Section -->
    <div class="log-section">
        <h2>User Purchase History</h2>
        <div class="filter-section">
            <input type="text" id="searchName" placeholder="Search by user name" oninput="displayPurchaseHistory()">
            <input type="date" id="startDateFilter" placeholder="Start Date" onchange="displayPurchaseHistory()">
            <input type="date" id="endDateFilter" placeholder="End Date" onchange="displayPurchaseHistory()">
        </div>
        <div id="purchaseHistory"></div>
        <p class="total-amount" id="totalPurchaseAmount"></p>
        <p class="total-fee" id="totalPurchaseFee"></p>
        <button class="clear-log-button" onclick="clearLog('purchase')">Clear Purchase Log</button>
    </div>

    <!-- User Donation History Section -->
    <div class="log-section">
        <h2>User Donation History</h2>
        <div class="filter-section">
            <input type="date" id="donationStartDateFilter" placeholder="Start Date" onchange="displayDonationHistory()">
            <input type="date" id="donationEndDateFilter" placeholder="End Date" onchange="displayDonationHistory()">
        </div>
        <div id="donationHistory"></div>
        <p class="total-amount" id="totalDonationAmount"></p>
        <p class="total-fee" id="totalDonationFee"></p>
        <button class="clear-log-button" onclick="clearLog('donation')">Clear Donation Log</button>
    </div>

    <script>
        function displayPurchaseHistory() {
            const transactions = JSON.parse(localStorage.getItem('userLogTransactions')) || [];
            const searchName = document.getElementById('searchName').value.toLowerCase();
            const startDate = document.getElementById('startDateFilter').value;
            const endDate = document.getElementById('endDateFilter').value;
            const purchaseHistoryDiv = document.getElementById('purchaseHistory');
            purchaseHistoryDiv.innerHTML = '';

            let totalPurchaseAmount = 0;
            let totalPurchaseFee = 0;

            transactions.forEach(transaction => {
                const transactionDate = new Date(transaction.date);
                const start = startDate ? new Date(startDate) : new Date('1970-01-01');
                const end = endDate ? new Date(endDate) : new Date();

                if (transaction.reason.startsWith("Buy") && transaction.userName.toLowerCase().includes(searchName) && transactionDate >= start && transactionDate <= end) {
                    const transactionDiv = document.createElement('div');
                    transactionDiv.className = 'transaction-item';
                    transactionDiv.innerHTML = `
                        <strong>Date:</strong> ${transaction.date} <br>
                        <strong>Customer Name:</strong> ${transaction.userName} <br>
                        <strong>Customer Email:</strong> ${transaction.userEmail} <br>
                        <strong>Customer Address:</strong> ${transaction.userAddress} <br>
                        <strong>Customer Account:</strong> ${transaction.sender} <br>
                        <strong>Recipient Account:</strong> ${transaction.recipient} <br>
                        <strong>Amount:</strong> ${transaction.amount} Ether <br>
                        <strong>Transaction Fee:</strong> ${transaction.transactionFee ? transaction.transactionFee : '0'} Ether <br>
                        <strong>Reason:</strong> ${transaction.reason} <br>                   
                    `;
                    purchaseHistoryDiv.appendChild(transactionDiv);

                    // Calculate the total purchase amount and fee
                    totalPurchaseAmount += parseFloat(transaction.amount);
                    totalPurchaseFee += parseFloat(transaction.transactionFee) || 0;  // Default to 0 if fee is invalid or undefined
                }
            });

            // Display total purchase amount and fee if there are purchases
            if (totalPurchaseAmount > 0) {
                document.getElementById('totalPurchaseAmount').innerText = `Total Purchase Amount: ${totalPurchaseAmount.toFixed(8)} Ether`;
                document.getElementById('totalPurchaseFee').innerText = `Total Transaction Fee: ${totalPurchaseFee.toFixed(8)} Ether`;
            } else {
                purchaseHistoryDiv.innerHTML = '<p>No purchases found.</p>';
                document.getElementById('totalPurchaseAmount').innerText = '';
                document.getElementById('totalPurchaseFee').innerText = '';
            }
        }

        function displayDonationHistory() {
            const transactions = JSON.parse(localStorage.getItem('userLogTransactions')) || [];
            const startDate = document.getElementById('donationStartDateFilter').value;
            const endDate = document.getElementById('donationEndDateFilter').value;
            const donationHistoryDiv = document.getElementById('donationHistory');
            donationHistoryDiv.innerHTML = '';

            let totalDonationAmount = 0;
            let totalDonationFee = 0;

            transactions.forEach(transaction => {
                const transactionDate = new Date(transaction.date);
                const start = startDate ? new Date(startDate) : new Date('1970-01-01');
                const end = endDate ? new Date(endDate) : new Date();

                if (transaction.reason === "Donation" && transactionDate >= start && transactionDate <= end) {
                    const transactionDiv = document.createElement('div');
                    transactionDiv.className = 'transaction-item';
                    transactionDiv.innerHTML = `
                        <strong>Date:</strong> ${transaction.date} <br>
                        <strong>Sender:</strong> ${transaction.sender} <br>
                        <strong>Recipient:</strong> ${transaction.recipient} <br>
                        <strong>Amount:</strong> ${transaction.amount} Ether <br>
                        <strong>Transaction Fee:</strong> ${transaction.transactionFee ? transaction.transactionFee : '0'} Ether <br>
                        <strong>Reason:</strong> ${transaction.reason}
                    `;
                    donationHistoryDiv.appendChild(transactionDiv);

                    // Calculate the total donation amount and fee
                    totalDonationAmount += parseFloat(transaction.amount);
                    totalDonationFee += parseFloat(transaction.transactionFee) || 0;  // Default to 0 if fee is invalid or undefined
                }
            });

            // Display total donation amount and fee if there are donations
            if (totalDonationAmount > 0) {
                document.getElementById('totalDonationAmount').innerText = `Total Donation Amount: ${totalDonationAmount.toFixed(8)} Ether`;
                document.getElementById('totalDonationFee').innerText = `Total Transaction Fee: ${totalDonationFee.toFixed(8)} Ether`;
            } else {
                donationHistoryDiv.innerHTML = '<p>No donations found.</p>';
                document.getElementById('totalDonationAmount').innerText = '';
                document.getElementById('totalDonationFee').innerText = '';
            }
        }

        function clearLog(type) {
            let transactions = JSON.parse(localStorage.getItem('userLogTransactions')) || [];

            const confirmation = confirm(`Are you sure you want to clear the ${type} log? This action cannot be undone.`);
            if (!confirmation) {
                return;
            }

            if (type === 'purchase') {
                transactions = transactions.filter(transaction => !transaction.reason.startsWith("Buy"));
            } else if (type === 'donation') {
                transactions = transactions.filter(transaction => transaction.reason !== "Donation");
            }

            localStorage.setItem('userLogTransactions', JSON.stringify(transactions));
            displayPurchaseHistory();
            displayDonationHistory();
        }

        // Initial display of history
        displayPurchaseHistory();
        displayDonationHistory();
    </script>
</body>
</html>
