
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
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
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        
        button {
            background-color: #dc3545;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border: none;
        }

        button:hover {
            background-color: #c82333;
        }

        .message {
            margin-top: 20px;
            color: green;
            font-size: 16px;
        }

        .product-details {
            margin-top: 20px;
            display: none;
            flex-direction: column;
            align-items: center;
        }

        .product-details img {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
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
    
    <h1>Delete Product</h1>
    
    <form id="deleteProductForm">
        <select id="productSelect" required>
            <option value="" disabled selected>Select a product to delete</option>
        </select>
        <div class="product-details" id="productDetails">
            <p><strong>Name:</strong> <span id="productName"></span></p>
            <img id="productImage" src="" alt="Product Image" />
            <p><strong>Price:</strong> <span id="productPrice"></span> ETH</p>
        </div>
        <button type="submit">Delete Product</button>
    </form>

    <p class="message" id="message"></p>

    <!-- loading -->
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

        let productName = "";
        let productImage = "";
        let productPriceEth = "";

// Load products and populate the select dropdown
async function loadProducts() {
    try {
        const products = await appleBoxContract.methods.getProducts().call();
        const productSelect = document.getElementById('productSelect');

        products.forEach(product => {
            if (product.active) {  // Only add active products
                const option = document.createElement('option');
                option.value = product.id;
                option.textContent = product.name;
                productSelect.appendChild(option);
            }
        });
    } catch (error) {
        console.error("Error loading products:", error);
    }
}

        // Show product details when selected
        document.getElementById('productSelect').addEventListener('change', async function() {
            const productId = this.value;
            try {
                const product = await appleBoxContract.methods.getProduct(productId).call();
                productName = product[1];
                productImage = product[2];
                productPriceEth = web3.utils.fromWei(product[3], 'ether');
                document.getElementById('productName').textContent = productName;
                document.getElementById('productImage').src = productImage;
                document.getElementById('productPrice').textContent = productPriceEth;
                document.getElementById('productDetails').style.display = 'flex';
            } catch (error) {
                console.error("Error fetching product:", error);
            }
        });

        document.getElementById('deleteProductForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const loadingOverlay = document.getElementById("loadingOverlay");
            loadingOverlay.classList.add("active"); // display loading

            const deleteButton = event.target.querySelector('button[type="submit"]');
            deleteButton.disabled = true; // disable button

            const productId = document.getElementById('productSelect').value;

            // Confirmation prompt before deletion
            if (!confirm(`Are you sure you want to delete the product "${productName}"?`)) {
                loadingOverlay.classList.remove("active"); // hide loading
                deleteButton.disabled = false; // re-enable button
                return;
            }

            try {
                await window.ethereum.request({ method: 'eth_requestAccounts' });
                const accounts = await web3.eth.getAccounts();
                const userAddress = accounts[0];

                await appleBoxContract.methods.deleteProduct(productId).send({
                    from: userAddress
                });

                document.getElementById('message').textContent = 'Product deleted successfully!';
                document.getElementById('deleteProductForm').reset();
                document.getElementById('productDetails').style.display = 'none';

                // Log admin action
                logAdminAction("Delete", productName, productImage, productPriceEth);

            } catch (error) {
                console.error("Error deleting product:", error);
                document.getElementById('message').textContent = "Error: " + error.message;
            } finally {
                loadingOverlay.classList.remove("active"); // ensure loading is removed
                deleteButton.disabled = false; // re-enable button
            }
        });

        // Function to log admin actions
        function logAdminAction(type, productName, productImage, productPriceEth) {
            const adminActions = JSON.parse(localStorage.getItem('adminActions')) || [];
            const date = new Date().toLocaleString();
            const details = `
                <strong>Product Name:</strong> ${productName} <br>
                <strong>Product Img URL:</strong> ${productImage} <br>
                <strong>Product Price:</strong> ${productPriceEth} ETH`;

            adminActions.unshift({ date, type, details });
            localStorage.setItem('adminActions', JSON.stringify(adminActions));
        }

        // Load products when the page loads
        window.onload = loadProducts;
    </script>
</body>
</html>
