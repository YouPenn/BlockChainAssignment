<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
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

        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            width: 300px;
            text-align: center;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        
        input { 
            width: 92%; /* don't change this */
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border: none;
        }

        button:hover {
            background-color: #218838;
        }

        .message {
            margin-top: 20px;
            color: green;
            font-size: 16px;
        }

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

        #imagePreview {
            margin-top: 10px;
            max-width: 100%;
            max-height: 200px;
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
                <a href="#">Activity Log</a>
                <ul>
                    <li><a href="adminLog.php">Admin Log</a></li>
                    <li><a href="userLog.php">User Log</a></li>
                </ul>
            </li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>
    
    <h1>Add New Product</h1>
    
    <form id="addProductForm">
        <input type="text" id="productName" placeholder="Product Name" required>
        <input type="file" id="productImage" accept="image/*" required>
        <img id="imagePreview" src="" alt="Image Preview" style="display:none;" />
        <input type="number" step="0.01" id="productPriceETH" placeholder="Price (Ether)" required>
        <input type="number" step="0.01" id="productPriceRM" placeholder="Price (RM)" required>
        <button type="submit">Add Product</button>
    </form>

    <p class="message" id="message"></p>

    <div class="loading-overlay" id="loadingOverlay">Processing your request, please wait...</div>

    <script src="https://cdn.jsdelivr.net/npm/web3@latest/dist/web3.min.js"></script>
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

        const contractAddress = "0x1131dD47C75bA7DF39Ad0ce21d8904BceD36f033"; // Replace with your deployed contract address

        const web3 = new Web3(window.ethereum);
        const appleBoxContract = new web3.eth.Contract(contractABI, contractAddress);
        let ethToRmRate = 0;

        async function fetchEthToRmRate() {
            try {
                const response = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=myr&x_cg_demo_api_key=CG-WTrF8ei6aCVPmMRnbpPDqBfH');
                const data = await response.json();
                ethToRmRate = data.ethereum.myr;
            } catch (error) {
                console.error("Error fetching ETH to RM rate:", error);
            }
        }

        document.getElementById('productImage').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('productPriceETH').addEventListener('input', function() {
            const ethPrice = parseFloat(this.value);
            if (!isNaN(ethPrice) && ethToRmRate) {
                document.getElementById('productPriceRM').value = (ethPrice * ethToRmRate).toFixed(2);
            }
        });

        document.getElementById('productPriceRM').addEventListener('input', function() {
            const rmPrice = parseFloat(this.value);
            if (!isNaN(rmPrice) && ethToRmRate) {
                document.getElementById('productPriceETH').value = (rmPrice / ethToRmRate).toFixed(8);
            }
        });

        document.getElementById('addProductForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const loadingOverlay = document.getElementById("loadingOverlay");
            loadingOverlay.classList.add("active"); // Display loading

            const productName = document.getElementById('productName').value;
            const productImageFile = document.getElementById('productImage').files[0];
            const productPriceETH = document.getElementById('productPriceETH').value;

            // Validation: ensure the price is positive
            if (productPriceETH <= 0) {
                document.getElementById('message').textContent = "Error: Price must be greater than zero.";
                loadingOverlay.classList.remove("active"); // Hide loading
                return;
            }

            try {
                // Upload image to ImgBB
                const imgBBApiKey = 'bdcf5034a83807831806a0591dbbebf4'; // Your ImgBB API key
                const formData = new FormData();
                formData.append('key', imgBBApiKey);
                formData.append('image', productImageFile);

                const imgbbResponse = await fetch('https://api.imgbb.com/1/upload', {
                    method: 'POST',
                    body: formData
                });

                const imgbbData = await imgbbResponse.json();

                if (!imgbbData.success) {
                    throw new Error('Image upload failed: ' + imgbbData.error.message);
                }

                const productImageUrl = imgbbData.data.url;

                const priceInWei = web3.utils.toWei(productPriceETH.toString(), 'ether');

                await window.ethereum.request({ method: 'eth_requestAccounts' });
                const accounts = await web3.eth.getAccounts();
                const userAddress = accounts[0];

                // Add product to the smart contract
                await appleBoxContract.methods.addProduct(productName, productImageUrl, priceInWei).send({
                    from: userAddress
                });

                document.getElementById('message').textContent = 'Product added successfully!';
                document.getElementById('addProductForm').reset();
                document.getElementById('imagePreview').style.display = 'none'; // Hide preview after successful submission

                // Log admin action
                logAdminAction("Add", productName, productImageUrl, productPriceETH);

            } catch (error) {
                console.error("Error adding product:", error);
                document.getElementById('message').textContent = "Error: " + error.message;
            } finally {
                loadingOverlay.classList.remove("active"); // Hide loading
            }
        });

        // Function to log admin actions
        function logAdminAction(type, productName, productImage, productPrice) {
            const adminActions = JSON.parse(localStorage.getItem('adminActions')) || [];
            const date = new Date().toLocaleString();
            const details = `
                <strong>Product Name:</strong> ${productName} <br>
                <strong>Product Img URL:</strong> ${productImage} <br>
                <strong>Product Price:</strong> ${productPrice} ETH`;

            adminActions.unshift({ date, type, details });
            localStorage.setItem('adminActions', JSON.stringify(adminActions));
        }

        window.onload = fetchEthToRmRate;

    </script>
</body>
</html>
