
<!DOCTYPE html>
<html>
<head>
    <title>About Us</title>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>

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

        .content, .donate-section {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 60%;
            width: 100%;
            text-align: center;
            margin-bottom: 40px;
        }

        .content p {
            font-size: 18px;
            line-height: 1.6;
            color: #495057;
        }

        .donate-section h2 {
            color: #343a40;
            margin-bottom: 20px;
        }

        .donate-section input[type="text"],
        .donate-section input[type="number"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .donate-section .amount-buttons {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin: 10px 0;
        }

        .donate-section .amount-button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            flex: 1;
            margin: 0 5px;
            transition: background-color 0.3s;
        }

        .donate-section .amount-button:hover {
            background-color: #0056b3;
        }

        .donate-section button {
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

        .donate-section button:hover {
            background-color: #218838;
        }

        .donate-section p#result {
            font-weight: bold;
            color: #28a745;
            margin: 10px 0;
        }

        /* loading */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.75);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 24px;
        }

        .loading-overlay.active {
            display: flex;
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

    <h1>About Us</h1>
    <div class="content">
        <p>Welcome to our e-commerce platform! We are dedicated to providing our customers with a seamless and enjoyable shopping experience. Our platform is designed to make purchasing your favorite products as easy and secure as possible.</p>
    </div>

    <div class="donate-section">
        <h2>Support Us</h2>
        <input type="text" id="recipientAddress" value="0x500D339043206e7459602Bd250C1781684B7901c" readonly>
        <input type="number" id="etherAmount" placeholder="Enter amount in ether" step="0.01">

        <div class="amount-buttons">
            <button class="amount-button" onclick="setAmount(0.1)">0.1</button>
            <button class="amount-button" onclick="setAmount(0.2)">0.2</button>
            <button class="amount-button" onclick="setAmount(0.5)">0.5</button>
            <button class="amount-button" onclick="setAmount(1.0)">1.0</button>
        </div>

        <button id="transferButton">Donate Ether</button>
        <p id="result"></p>
    </div>

    <!-- loading -->
    <div class="loading-overlay" id="loadingOverlay">Processing your transaction, please wait...</div>

    <script>
        const transferABI = [
	{
		"inputs": [],
		"stateMutability": "nonpayable",
		"type": "constructor"
	},
	{
		"inputs": [],
		"name": "owner",
		"outputs": [
			{
				"internalType": "address",
				"name": "",
				"type": "address"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [
			{
				"internalType": "address payable",
				"name": "_recipient",
				"type": "address"
			}
		],
		"name": "transferEther",
		"outputs": [],
		"stateMutability": "payable",
		"type": "function"
	},
	{
		"inputs": [],
		"name": "withdraw",
		"outputs": [],
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"stateMutability": "payable",
		"type": "receive"
	}
];

        const transferAddress = "0xbd5726FacBaD62CcaA2a3361bE4f026a25c1DC1F"; // Replace with your deployed contract address

        function setAmount(amount) {
            document.getElementById("etherAmount").value = amount;
        }

        window.addEventListener('load', async () => {
            if (typeof window.ethereum !== 'undefined') {
                const web3 = new Web3(window.ethereum);
                const transferContract = new web3.eth.Contract(transferABI, transferAddress);
                try {
                    await window.ethereum.request({ method: 'eth_requestAccounts' });
                } catch (error) {
                    console.error("User denied account access:", error);
                }

                document.getElementById("transferButton").onclick = async () => {
                    const recipientAddress = document.getElementById("recipientAddress").value;
                    const etherAmount = parseFloat(document.getElementById("etherAmount").value);
                    const loadingOverlay = document.getElementById("loadingOverlay");

                    if (isNaN(etherAmount) || etherAmount <= 0) {
                        document.getElementById("result").innerText = "Please enter a valid amount of ether.";
                        return;
                    }
                    
                    
                    
                    if (etherAmount > 0.4) {
                        const proceed = confirm("The amount you entered is greater than 0.5 ether. Are you sure you want to proceed?");
                        if (!proceed) {
                            document.getElementById("result").innerText = "Transaction canceled.";
                            return;
                        }
                    }
                    
                    

                    const accounts = await web3.eth.getAccounts();
                    const userAddress = accounts[0];

                    try {
                        loadingOverlay.classList.add("active"); // display loading

                        const transactionReceipt = await transferContract.methods.transferEther(recipientAddress).send({
                            from: userAddress,
                            value: web3.utils.toWei(etherAmount.toString(), 'ether')
                        });

                        document.getElementById("result").innerText = "Thank you for your support! Your contribution helps us maintain and improve our services.";
                        addTransactionToHistory(userAddress, recipientAddress, etherAmount, "Donation");
                    } catch (error) {
                        console.error("Error in donation:", error);

                        if (error.code === 4001) {
                            // This is the MetaMask user rejection error
                            document.getElementById("result").innerText = "Transaction was rejected by the user.";
                        } else {
                            // General error
                            document.getElementById("result").innerText = "An error occurred. Please try again.";
                        }
                    } finally {
                        loadingOverlay.classList.remove("active"); // hide loading
                    }
                };
            } else {
                console.log('Non-Ethereum browser detected. You should consider trying MetaMask!');
            }
        });

        // Add transaction to both history and user log
        function addTransactionToHistory(sender, recipient, amount, reason) {
            const transaction = {
                date: new Date().toLocaleString(),
                sender,
                recipient,
                amount,
                reason
            };

            // Store in history transactions
            const historyTransactions = JSON.parse(localStorage.getItem('transactions')) || [];
            historyTransactions.unshift(transaction);
            localStorage.setItem('transactions', JSON.stringify(historyTransactions));

            // Store in user log transactions
            const userLogTransactions = JSON.parse(localStorage.getItem('userLogTransactions')) || [];
            userLogTransactions.unshift(transaction);
            localStorage.setItem('userLogTransactions', JSON.stringify(userLogTransactions));
        }
    </script>
</body>
</html>