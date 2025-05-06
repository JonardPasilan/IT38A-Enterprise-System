<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MedTrack Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      margin: 0;
      display: flex;
      height: 100vh;
      background-color: #4a0000;
    }

    .sidebar {
      width: 200px;
      background: url('pharmacy image.jpg') no-repeat center center;
      background-size: cover;
      display: flex;
      flex-direction: column;
      padding: 20px 10px;
      gap: 10px;
    }

    .sidebar button {
      background: white;
      border: none;
      padding: 10px;
      border-radius: 10px;
      text-align: left;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    .sidebar button:hover {
      background: #ccc;
    }

    .main {
      flex: 1;
      padding: 20px 40px;
      color: white;
      background-color: #600000;
      overflow-y: auto;
    }

    .main-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .main-header h1 {
      margin: 0;
    }

    .search-bar {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .search-bar input {
      padding: 5px 10px;
      border-radius: 20px;
      border: none;
      outline: none;
    }

    .order-section {
      margin-top: 20px;
    }

    .order-section h2 {
      margin-bottom: 5px;
    }

    .catalog-center {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .medicines {
      display: flex;
      gap: 20px;
    }

    .medicine-card {
      background: #eee;
      color: black;
      border-radius: 10px;
      padding: 15px;
      width: 180px;
      text-align: center;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }

    .medicine-card button {
      margin-top: 10px;
      padding: 8px;
      background: black;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .bottom-buttons {
      margin-top: auto;
    }

    .bottom-buttons button {
      width: 100%;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <button>Order Medicines</button>
  <button>Medication Reminders</button>
  <button>Prescription History</button>
  <button>Notifications</button>
  <div class="bottom-buttons">
    <button>Settings</button>
    <button>Log-out</button>
  </div>
</div>

<div class="main">
  <div class="main-header">
    <h1>MedTrack</h1>
    <div class="search-bar">
      <input type="text" placeholder="Search...">
      <span style="background:white; padding:5px 10px; border-radius: 50%;">🔍</span>
    </div>
  </div>

  <div class="order-section">
    <h2>Order Medicines</h2>
    <p>Browse and purchase medicine easily.</p>
    <div class="catalog-center">
      <div class="medicines">
        <div class="medicine-card">
          <h3>Medicine 1</h3>
          <p>Paracetamol</p>
          <p>₱5.00</p>
          <button>Add to Cart</button>
        </div>
        <div class="medicine-card">
          <h3>Medicine 2</h3>
          <p>Ibuprofen</p>
          <p>₱10.00</p>
          <button>Add to Cart</button>
        </div>
        <div class="medicine-card">
          <h3>Medicine 3</h3>
          <p>Antibiotic</p>
          <p>₱0.00</p>
          <button>Add to Cart</button>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
