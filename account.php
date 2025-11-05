<!DOCTYPE html>
<html>
<head>
    <title>Account Information</title>
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

        .content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 60%;
            text-align: center;
            margin-bottom: 40px;
            width: 100%;
        }

        .content p {
            font-size: 18px;
            line-height: 1.6;
            color: #495057;
        }

        .content .address {
            font-weight: bold;
            color: #28a745;
            margin-top: 20px;
            word-break: break-word;
        }

        .content .amount {
            font-size: 24px;
            color: #007bff;
            margin-top: 20px;
        }

        .content .amount-rm {
            font-size: 20px;
            color: #ff7f00;
            margin-top: 10px;
        }

        .switch-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            border: none;
        }

        .switch-button:hover {
            background-color: #0056b3;
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

    <h1>Account Information</h1>
    <div class="content">
        <p>Account Address:</p>
        <p class="address" id="accountAddress"></p>

        <p>Total Amount in MetaMask:</p>
        <p class="amount" id="totalAmount"></p>

        <p>Equivalent in RM:</p>
        <p class="amount-rm" id="totalAmountRM"></p>

        <button class="switch-button" id="switchAccount">Switch Account</button>
    </div>

    <script>
        let ethToRmRate = 0;

        async function fetchEthToRmRate() {
            try {
                const response = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=myr');
                const data = await response.json();
                ethToRmRate = data.ethereum.myr;
            } catch (error) {
                console.error("Error fetching ETH to RM rate:", error);
            }
        }

        async function updateAccountInfo() {
            const addressElement = document.getElementById("accountAddress");
            const amountElement = document.getElementById("totalAmount");
            const amountRMElement = document.getElementById("totalAmountRM");

            if (typeof window.ethereum !== 'undefined') {
                const web3 = new Web3(window.ethereum);
                try {
                    await window.ethereum.request({ method: 'eth_requestAccounts' });
                    const accounts = await web3.eth.getAccounts();
                    const accountAddress = accounts[0];

                    addressElement.innerText = accountAddress;

                    const balance = await web3.eth.getBalance(accountAddress);
                    const balanceInEth = web3.utils.fromWei(balance, 'ether');

                    amountElement.innerText = balanceInEth + " ETH";

                    // Convert ETH to RM and display it
                    if (ethToRmRate) {
                        const balanceInRM = (balanceInEth * ethToRmRate).toFixed(2);
                        amountRMElement.innerText = "RM " + balanceInRM;
                    } else {
                        amountRMElement.innerText = "N/A";
                    }
                } catch (error) {
                    console.error("Error retrieving account information:", error);
                    addressElement.innerText = "Error retrieving account information.";
                    amountElement.innerText = "N/A";
                    amountRMElement.innerText = "N/A";
                }
            } else {
                console.log('Non-Ethereum browser detected. You should consider trying MetaMask!');
                addressElement.innerText = "MetaMask not detected. Please install MetaMask.";
                amountElement.innerText = "N/A";
                amountRMElement.innerText = "N/A";
            }
        }

        document.getElementById('switchAccount').addEventListener('click', async () => {
            try {
                await window.ethereum.request({ method: 'wallet_requestPermissions', params: [{ eth_accounts: {} }] });
                updateAccountInfo(); // Refresh account info after switching
            } catch (error) {
                console.error("Error switching account:", error);
            }
        });

        window.onload = async () => {
            await fetchEthToRmRate(); // Fetch ETH to RM rate on load
            updateAccountInfo(); // Update account information
        };
    </script>
</body>
</html>
