


<!DOCTYPE html>

<html>
<head>
    <title>Admin Panel</title>
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
        .buttons-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* Creates three columns */
            grid-template-rows: repeat(2, 1fr); /* Creates two rows */
            gap: 10px; /* Adds space between the buttons */
            width: 80%; /* Set a maximum width for the container */
            max-width: 450px; /* Ensures all buttons are the same width */
            height: 200px; /* Ensures all buttons are the same height */
            margin-top: 20px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
            width: 100%; /* Makes each button fill the grid cell */
            height: 100%; /* Makes each button fill the grid cell */
        }
        button:hover {
            background-color: #218838;
        }
        .clear-button {
            background-color: #dc3545;
        }
        .clear-button:hover {
            background-color: #c82333;
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

    <h1>Welcome to the Admin Panel</h1>
    
    <!-- Grid layout 3x2 -->
    <div class="buttons-container">
        <button onclick="window.location.href='add.php';">Add Product</button>
        <button onclick="window.location.href='edit.php';">Edit Product</button>
        <button onclick="window.location.href='delete.php';">Delete Product</button>
        <button onclick="window.location.href='adminLog.php';">Admin Log</button>
        <button onclick="window.location.href='userLog.php';">User Log</button>
        <button onclick="window.location.href='index.php';">Logout</button>
    </div>

</body>
</html>
