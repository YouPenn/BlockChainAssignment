<!DOCTYPE html>
<html>
<head>
    <title>History</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
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
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 20px;
        }
        h1 {
            color: #343a40;
        }
        #transactionHistory {
            width: 100%;
            max-width: 60%;
            margin-top: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            position: relative;
        }
        .transaction-item {
            border-bottom: 1px solid #dee2e6;
            padding: 10px 0;
            color: #495057;
            position: relative;
        }
        .transaction-item:last-child {
            border-bottom: none;
        }
        button {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #218838;
        }
        .clear-button {
            background-color: #dc3545;
            position: absolute;
            top: 15px;
            right: 15px;
        }
        .clear-button:hover {
            background-color: #c82333;
        }
        /* Filter section styles */
        .filter-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            max-width: 60%;
            width: 100%;
        }
        .filter-section select, .filter-section input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            width: 32%;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="index.php">AppleBox</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="account.php">Account</a></li>
            <li><a href="adminLogin.php">Admin Login</a></li>
        </ul>
    </nav>

    <h1>Transaction History</h1>
    <div class="filter-section">
        <select id="filterReason">
            <option value="all">All Transactions</option>
            <option value="Buy">Buy</option>
            <option value="Donation">Donation</option>
        </select>
        <input type="date" id="filterStartDate" placeholder="Start Date">
        <input type="date" id="filterEndDate" placeholder="End Date">
    </div>
    
    <div id="transactionHistory"></div>

    <script>
        let transactions = JSON.parse(localStorage.getItem('transactions')) || [];

        function displayTransactionHistory() {
            const filterReason = document.getElementById('filterReason').value;
            const filterStartDate = document.getElementById('filterStartDate').value;
            const filterEndDate = document.getElementById('filterEndDate').value;

            const filteredTransactions = transactions.filter(transaction => {
                let transactionDate = new Date(transaction.date);
                let matchesReason = filterReason === 'all' || transaction.reason.includes(filterReason);
                let matchesDate = true;

                if (filterStartDate) {
                    matchesDate = matchesDate && transactionDate >= new Date(filterStartDate);
                }

                if (filterEndDate) {
                    matchesDate = matchesDate && transactionDate <= new Date(filterEndDate);
                }

                return matchesReason && matchesDate;
            });

            const transactionHistoryDiv = document.getElementById('transactionHistory');
            transactionHistoryDiv.innerHTML = ''; // Clear existing content

            let totalAmount = 0;
            let totalTransactionFee = 0;

            filteredTransactions.forEach((transaction, index) => {
                const transactionDiv = document.createElement('div');
                transactionDiv.className = 'transaction-item';

                let transactionDetails = `
                    <strong>Date:</strong> ${transaction.date} <br>
                `;

                if (!transaction.reason.includes("Donation")) {
                    transactionDetails += `
                        <strong>Customer Name:</strong> ${transaction.userName} <br>
                        <strong>Customer Email:</strong> ${transaction.userEmail} <br>
                        <strong>Customer Address:</strong> ${transaction.userAddress} <br>
                    `;
                }

                transactionDetails += `
                    <strong>Customer Account:</strong> ${transaction.sender} <br>
                    <strong>Recipient Account:</strong> ${transaction.recipient} <br>
                    <strong>Amount:</strong> ${transaction.amount} Ether <br>
                    <strong>Transaction Fee:</strong> ${parseFloat(transaction.transactionFee || 0).toFixed(18)} Ether <br>
                    <strong>Reason:</strong> ${transaction.reason} <br>
                `;

                transactionDiv.innerHTML = transactionDetails;

                // Add a button to remove the specific transaction
                const removeButton = document.createElement('button');
                removeButton.className = 'clear-button';
                removeButton.textContent = 'Remove';
                removeButton.onclick = () => removeTransaction(index);

                transactionDiv.appendChild(removeButton);
                transactionHistoryDiv.appendChild(transactionDiv);

                totalAmount += parseFloat(transaction.amount);
                totalTransactionFee += parseFloat(transaction.transactionFee || 0);
            });

            if (filteredTransactions.length > 0) {
                const totalAmountDiv = document.createElement('div');
                totalAmountDiv.className = 'transaction-item';
                totalAmountDiv.innerHTML = `<strong>Total Amount:</strong> ${totalAmount.toFixed(18)} Ether`;

                const totalFeeDiv = document.createElement('div');
                totalFeeDiv.className = 'transaction-item';
                totalFeeDiv.innerHTML = `<strong>Total Transaction Fee:</strong> ${totalTransactionFee.toFixed(18)} Ether`;

                transactionHistoryDiv.appendChild(totalAmountDiv);
                transactionHistoryDiv.appendChild(totalFeeDiv);
            } else {
                transactionHistoryDiv.innerHTML = '<p>No transactions found.</p>';
            }
        }

        // Remove a specific transaction
        function removeTransaction(index) {
            if (confirm("Are you sure you want to remove this transaction?")) {
                transactions.splice(index, 1);
                localStorage.setItem('transactions', JSON.stringify(transactions));
                displayTransactionHistory();
            }
        }

        // Add event listeners for filters
        document.getElementById('filterReason').addEventListener('change', displayTransactionHistory);
        document.getElementById('filterStartDate').addEventListener('change', displayTransactionHistory);
        document.getElementById('filterEndDate').addEventListener('change', displayTransactionHistory);

        displayTransactionHistory();

    </script>
</body>
</html>
