<!DOCTYPE html>
<html>
<head>
    <title>AppleBox</title>
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

        .vending-machine {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 50px;
            width: 80%;
        }

        .product {
            background-color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .product h3 {
            margin: 10px 0;
        }

        .product button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .product button:hover {
            background-color: #218838;
        }

        #result {
            margin-top: 20px;
            font-size: 18px;
            color: #495057;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
        }

        .modal-content input[type="text"], .modal-content input[type="submit"], .modal-content input[type="email"] {
            width: 94%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .modal-content input[type="submit"] {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            width: 100%;
        }

        .modal-content input[type="submit"]:hover {
            background-color: #218838;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Loading overlay */
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

    <h1>AppleBox</h1>
    <div class="vending-machine" id="vendingMachine"></div>

    <p id="result"></p>

    <!-- loading -->
    <div class="loading-overlay" id="loadingOverlay">Processing your purchase, please wait...</div>

    <!-- The Modal -->
    <div id="purchaseModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Enter Your Details</h2>
            <form id="userDetailsForm">
                <input type="text" id="userName" placeholder="Your Name" required>
                <input type="email" id="userEmail" placeholder="Your Email" required>
                <input type="text" id="userAddress" placeholder="Your Address" required>
                <input type="submit" value="Confirm Purchase">
            </form>
        </div>
    </div>

    <script>
        const contractABI = [
	{
		"inputs": [],
		"stateMutability": "nonpayable",
		"type": "constructor"
	},
	{
		"anonymous": false,
		"inputs": [
			{
				"indexed": false,
				"internalType": "uint256",
				"name": "id",
				"type": "uint256"
			},
			{
				"indexed": false,
				"internalType": "string",
				"name": "name",
				"type": "string"
			},
			{
				"indexed": false,
				"internalType": "uint256",
				"name": "price",
				"type": "uint256"
			},
			{
				"indexed": false,
				"internalType": "address",
				"name": "owner",
				"type": "address"
			}
		],
		"name": "ProductAdded",
		"type": "event"
	},
	{
		"anonymous": false,
		"inputs": [
			{
				"indexed": false,
				"internalType": "uint256",
				"name": "id",
				"type": "uint256"
			}
		],
		"name": "ProductDeleted",
		"type": "event"
	},
	{
		"anonymous": false,
		"inputs": [
			{
				"indexed": false,
				"internalType": "uint256",
				"name": "id",
				"type": "uint256"
			},
			{
				"indexed": false,
				"internalType": "string",
				"name": "name",
				"type": "string"
			},
			{
				"indexed": false,
				"internalType": "uint256",
				"name": "price",
				"type": "uint256"
			},
			{
				"indexed": false,
				"internalType": "address",
				"name": "owner",
				"type": "address"
			}
		],
		"name": "ProductUpdated",
		"type": "event"
	},
	{
		"inputs": [
			{
				"internalType": "string",
				"name": "_name",
				"type": "string"
			},
			{
				"internalType": "string",
				"name": "_image",
				"type": "string"
			},
			{
				"internalType": "uint256",
				"name": "_price",
				"type": "uint256"
			}
		],
		"name": "addProduct",
		"outputs": [],
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "_id",
				"type": "uint256"
			}
		],
		"name": "deleteProduct",
		"outputs": [],
		"stateMutability": "nonpayable",
		"type": "function"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"name": "deletedIds",
		"outputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "_id",
				"type": "uint256"
			}
		],
		"name": "getProduct",
		"outputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			},
			{
				"internalType": "string",
				"name": "",
				"type": "string"
			},
			{
				"internalType": "string",
				"name": "",
				"type": "string"
			},
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			},
			{
				"internalType": "address",
				"name": "",
				"type": "address"
			},
			{
				"internalType": "bool",
				"name": "",
				"type": "bool"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [],
		"name": "getProducts",
		"outputs": [
			{
				"components": [
					{
						"internalType": "uint256",
						"name": "id",
						"type": "uint256"
					},
					{
						"internalType": "string",
						"name": "name",
						"type": "string"
					},
					{
						"internalType": "string",
						"name": "image",
						"type": "string"
					},
					{
						"internalType": "uint256",
						"name": "price",
						"type": "uint256"
					},
					{
						"internalType": "address",
						"name": "owner",
						"type": "address"
					},
					{
						"internalType": "bool",
						"name": "active",
						"type": "bool"
					}
				],
				"internalType": "struct AppleBox.Product[]",
				"name": "",
				"type": "tuple[]"
			}
		],
		"stateMutability": "view",
		"type": "function"
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
		"inputs": [],
		"name": "productCount",
		"outputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"name": "products",
		"outputs": [
			{
				"internalType": "uint256",
				"name": "id",
				"type": "uint256"
			},
			{
				"internalType": "string",
				"name": "name",
				"type": "string"
			},
			{
				"internalType": "string",
				"name": "image",
				"type": "string"
			},
			{
				"internalType": "uint256",
				"name": "price",
				"type": "uint256"
			},
			{
				"internalType": "address",
				"name": "owner",
				"type": "address"
			},
			{
				"internalType": "bool",
				"name": "active",
				"type": "bool"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "_id",
				"type": "uint256"
			},
			{
				"internalType": "string",
				"name": "_name",
				"type": "string"
			},
			{
				"internalType": "string",
				"name": "_image",
				"type": "string"
			},
			{
				"internalType": "uint256",
				"name": "_price",
				"type": "uint256"
			}
		],
		"name": "updateProduct",
		"outputs": [],
		"stateMutability": "nonpayable",
		"type": "function"
	}
];

        
        const contractAddress = "0x1131dD47C75bA7DF39Ad0ce21d8904BceD36f033"; // Deployed contract address
        const receiverAddress = "0x500D339043206e7459602Bd250C1781684B7901c"; // Receiver address

        const web3 = new Web3(window.ethereum);
        const appleBoxContract = new web3.eth.Contract(contractABI, contractAddress);

        let ethToRmRate = 0;
        let selectedProduct = { ethPrice: 0, name: '' };

        async function fetchEthToRmRate() {
            try {
                const response = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=myr&x_cg_demo_api_key=CG-WTrF8ei6aCVPmMRnbpPDqBfH');
                const data = await response.json();
                ethToRmRate = data.ethereum.myr;
            } catch (error) {
                console.error("Error fetching ETH to RM rate:", error);
            }
        }

        async function loadProducts() {
            try {
                await fetchEthToRmRate();
                const products = await appleBoxContract.methods.getProducts().call();
                const vendingMachineDiv = document.getElementById('vendingMachine');

                products.forEach(product => {
                    if (!product.active) return; // Skip inactive products

                    const productDiv = document.createElement('div');
                    productDiv.classList.add('product');

                    const productImage = document.createElement('img');
                    productImage.src = product.image || 'images/default-product.png';
                    productImage.alt = product.name;

                    const productName = document.createElement('h3');
                    productName.textContent = product.name;

                    const productPrice = document.createElement('p');
                    const ethPrice = web3.utils.fromWei(product.price, 'ether');
                    const rmPrice = (ethPrice * ethToRmRate).toFixed(2);

                    productPrice.innerHTML = `Price(ETH): ${ethPrice} <br> Price(RM): ${rmPrice}`;

                    const buyButton = document.createElement('button');
                    buyButton.textContent = 'Buy Now';
                    buyButton.onclick = () => openPurchaseModal(ethPrice, product.name);

                    productDiv.appendChild(productImage);
                    productDiv.appendChild(productName);
                    productDiv.appendChild(productPrice);
                    productDiv.appendChild(buyButton);

                    vendingMachineDiv.appendChild(productDiv);
                });
            } catch (error) {
                console.error("Error loading products:", error);
            }
        }

        function openPurchaseModal(price, productName) {
            selectedProduct = { ethPrice: price, name: productName };
            document.getElementById('purchaseModal').style.display = 'block';
        }

        // Close the modal when the user clicks the close button
        document.querySelector('.close').onclick = function() {
            document.getElementById('purchaseModal').style.display = 'none';
        };

        // Handle form submission and proceed with the purchase
        document.getElementById('userDetailsForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const userName = document.getElementById('userName').value;
            const userEmail = document.getElementById('userEmail').value;
            const userAddress = document.getElementById('userAddress').value;

            if (!userName || !userEmail || !userAddress) {
                alert("Please fill in all fields.");
                return;
            }

            document.getElementById('purchaseModal').style.display = 'none';
            await buyProduct(selectedProduct.ethPrice, selectedProduct.name, userName, userEmail, userAddress);
        });

        async function buyProduct(price, productName, userName, userEmail, userAddress) {
            const loadingOverlay = document.getElementById("loadingOverlay");
            loadingOverlay.classList.add("active");

            if (window.ethereum) {
                try {
                    await window.ethereum.request({ method: 'eth_requestAccounts' });
                    const accounts = await web3.eth.getAccounts();
                    const userEthAddress = accounts[0];

                    // 发起交易并获取交易回执
                    const transaction = await web3.eth.sendTransaction({
                        from: userEthAddress,
                        to: receiverAddress,
                        value: web3.utils.toWei(price.toString(), 'ether')
                    });

                    // 获取交易回执
                    const transactionReceipt = await web3.eth.getTransactionReceipt(transaction.transactionHash);

                    // 获取Gas使用量和Gas价格
                    const gasUsed = transactionReceipt.gasUsed;
                    const transactionDetails = await web3.eth.getTransaction(transaction.transactionHash);
                    const gasPrice = transactionDetails.gasPrice;

                    // 计算交易费用
                    const transactionFee = web3.utils.fromWei((gasUsed * gasPrice).toString(), 'ether');

                    document.getElementById("result").innerText = `Purchase of ${productName} by ${userName} was successful!
                    \nTransaction Fee: ${transactionFee} ETH`;

                    // 将交易费用记录到历史记录中
                    addTransactionToHistory(userName, userEmail, userAddress, userEthAddress, receiverAddress, price, transactionFee, `Buy ${productName}`);
                } catch (error) {
                    console.error("Error in transaction:", error);
                    document.getElementById("result").innerText = "Error: " + error.message;
                } finally {
                    loadingOverlay.classList.remove("active");
                }
            } else {
                console.log('Non-Ethereum browser detected. You should consider trying MetaMask!');
                loadingOverlay.classList.remove("active");
            }
        }

        // Add transaction to both history and user log
        function addTransactionToHistory(userName, userEmail, userAddress, sender, recipient, amount, transactionFee, reason) {
            const transaction = {
                date: new Date().toLocaleString(),
                userName,
                userEmail,
                userAddress,
                sender,
                recipient,
                amount,
                transactionFee,  // 新增交易费用
                reason
            };

            // 存储在历史交易记录中
            const historyTransactions = JSON.parse(localStorage.getItem('transactions')) || [];
            historyTransactions.unshift(transaction);
            localStorage.setItem('transactions', JSON.stringify(historyTransactions));

            // 存储在用户日志记录中
            const userLogTransactions = JSON.parse(localStorage.getItem('userLogTransactions')) || [];
            userLogTransactions.unshift(transaction);
            localStorage.setItem('userLogTransactions', JSON.stringify(userLogTransactions));
        }

        window.onload = loadProducts;
    </script>
</body>
</html>
