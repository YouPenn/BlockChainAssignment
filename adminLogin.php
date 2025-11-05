<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
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
            height: 70vh;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        input {
            width: 92%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        .back-button {
            background-color: #007bff;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .error {
            color: #dc3545;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <form id="loginForm">
        <h2>Admin Login</h2>
        <input type="text" id="adminID" placeholder="Admin ID" required>
        <input type="password" id="adminPassword" placeholder="Password" required>
        <button type="submit">Login</button>
        <button type="button" class="back-button" onclick="window.location.href='index.php';">Back</button>
        <p class="error" id="errorMsg"></p>
    </form>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const adminID = document.getElementById('adminID').value;
    const adminPassword = document.getElementById('adminPassword').value;
    const errorMsg = document.getElementById('errorMsg');
    const loginButton = event.target.querySelector('button[type="submit"]');
    
    // Disable the button to prevent multiple submissions
    loginButton.disabled = true;
    
    // Validate credentials
    if (adminID === 'A001' && adminPassword === 'adminpassword') {
        // Redirect to admin panel
        window.location.href = 'admin.php';
    } else if (adminID === 'A002' && adminPassword === 'adminpassword') {
        // Redirect to admin panel
        window.location.href = 'admin.php';
    } else {
        // Display error message and re-enable the button
        errorMsg.textContent = 'Invalid Admin ID or Password.';
        loginButton.disabled = false;
    }
});

// Focus on the Admin ID field when the page loads
window.onload = function() {
    document.getElementById('adminID').focus();
};
    </script>
</body>
</html>
