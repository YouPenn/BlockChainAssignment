

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
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
                <a href="#">Activity Log</a> <!-- Dropdown Trigger -->
                <ul>
                    <li><a href="adminLog.php">Admin Log</a></li>
                    <li><a href="userLog.php">User Log</a></li>
                </ul>
            </li>
            <li><a href="index.php">Logout</a></li>
        </ul>
    </nav>
    
    <h1>Edit Product</h1>
    
    <form id="editProductForm">
        <select id="productSelect" required>
            <option value="" disabled selected>Select a product to edit</option>
        </select>
        <input type="text" id="productName" placeholder="Product Name" required>
        <input type="file" id="productImage" accept="image/*">
        <img id="imagePreview" src="" alt="Image Preview" style="display:none;" />
        <input type="number" step="0.01" id="productPrice" placeholder="Price (Ether)" required>
        <button type="submit">Update Product</button>
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

        let oldProductName = "";
        let oldProductImage = "";
        let oldProductPriceEth = "";

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


        // When a product is selected, fill in the form with its details and store old details
        document.getElementById('productSelect').addEventListener('change', async function() {
            const productId = this.value;
            try {
                const product = await appleBoxContract.methods.getProduct(productId).call();
                oldProductName = product[1];
                oldProductImage = product[2];
                oldProductPriceEth = web3.utils.fromWei(product[3], 'ether');
                document.getElementById('productName').value = oldProductName;
                document.getElementById('productImage').value = "";
                document.getElementById('productPrice').value = oldProductPriceEth;

                // Display the old product image as a preview
                const preview = document.getElementById('imagePreview');
                preview.src = oldProductImage;
                preview.style.display = 'block';
            } catch (error) {
                console.error("Error fetching product:", error);
            }
        });

        // Image preview when a new image is selected
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

        document.getElementById('editProductForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const loadingOverlay = document.getElementById("loadingOverlay");
            loadingOverlay.classList.add("active");

            const productId = document.getElementById('productSelect').value;
            const newProductName = document.getElementById('productName').value;
            const newProductImageFile = document.getElementById('productImage').files[0];
            const newProductPrice = document.getElementById('productPrice').value;

            // Validation: ensure the price is positive
            if (newProductPrice <= 0) {
                document.getElementById('message').textContent = "Error: Price must be greater than zero.";
                loadingOverlay.classList.remove("active");
                return;
            }

            let newProductImageUrl = oldProductImage; // Keep the old image if no new image is uploaded

            if (newProductImageFile) {
                try {
                    // Upload new image to ImgBB
                    const imgBBApiKey = 'bdcf5034a83807831806a0591dbbebf4'; // Your ImgBB API key
                    const formData = new FormData();
                    formData.append('key', imgBBApiKey);
                    formData.append('image', newProductImageFile);

                    const imgbbResponse = await fetch('https://api.imgbb.com/1/upload', {
                        method: 'POST',
                        body: formData
                    });

                    const imgbbData = await imgbbResponse.json();

                    if (!imgbbData.success) {
                        throw new Error('Image upload failed: ' + imgbbData.error.message);
                    }

                    newProductImageUrl = imgbbData.data.url;

                } catch (error) {
                    console.error("Error uploading image:", error);
                    document.getElementById('message').textContent = "Error: " + error.message;
                    loadingOverlay.classList.remove("active");
                    return;
                }
            }

            const priceInWei = web3.utils.toWei(newProductPrice.toString(), 'ether');

            try {
                await window.ethereum.request({ method: 'eth_requestAccounts' });
                const accounts = await web3.eth.getAccounts();
                const userAddress = accounts[0];

                await appleBoxContract.methods.updateProduct(productId, newProductName, newProductImageUrl, priceInWei).send({
                    from: userAddress
                });

                document.getElementById('message').textContent = 'Product updated successfully!';

                // Log admin action
                logAdminAction("Edit", oldProductName, oldProductImage, oldProductPriceEth, newProductName, newProductImageUrl, newProductPrice);

            } catch (error) {
                console.error("Error updating product:", error);
                document.getElementById('message').textContent = "Error: " + error.message;
            } finally {
                loadingOverlay.classList.remove("active");
            }
        });

        // Function to log admin actions
        function logAdminAction(type, oldProductName, oldProductImage, oldProductPriceEth, newProductName, newProductImage, newProductPriceEth) {
            const adminActions = JSON.parse(localStorage.getItem('adminActions')) || [];
            const date = new Date().toLocaleString();
            const details = `
                <strong>Old Product Name:</strong> ${oldProductName} <br>
                <strong>Old Product Img URL:</strong> ${oldProductImage} <br>
                <strong>Old Product Price:</strong> ${oldProductPriceEth} ETH <br>
                <strong>New Product Name:</strong> ${newProductName} <br>
                <strong>New Product Img URL:</strong> ${newProductImage} <br>
                <strong>New Product Price:</strong> ${newProductPriceEth} ETH`;

            adminActions.unshift({ date, type, details });
            localStorage.setItem('adminActions', JSON.stringify(adminActions));
        }

        // Load products when the page loads
        window.onload = loadProducts;
    </script>
</body>
</html>
