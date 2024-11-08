<?php
// Start the session
session_start();

// Check if the user is logged in by checking the session
if (!isset($_SESSION['username'])) {
    header('Location: ../Login/Login Page.html'); // Redirect to login if not logged in
    exit();
}

?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transactions_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get total expenses for a specific month
function getMonthlyExpenses($conn, $month, $year) {
    $sql = "SELECT SUM(amount) as total FROM transactions1 
            WHERE MONTH(date) = ? AND YEAR(date) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// Get current and last month's expenses
$currentMonth = date('n');
$currentYear = date('Y');
$lastMonth = $currentMonth - 1;
$lastMonthYear = $currentYear;

if ($lastMonth === 0) {
    $lastMonth = 12;
    $lastMonthYear--;
}

$currentMonthExpenses = getMonthlyExpenses($conn, $currentMonth, $currentYear);
$lastMonthExpenses = getMonthlyExpenses($conn, $lastMonth, $lastMonthYear);

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="style.css" />
    <title>Dashboard</title>
  </head>

  <body id="myFont">
    <!-- Sidebar section -->
    <nav class="sidebar show-sidebar">
      <div class="logo-items flex">
        <h3>m/m</h3>
        <i class="fa-solid fa-xmark" id="sidebar-close"></i>
      </div>
      <br />
      <div class="menu_container">
        <div class="menu-items">
          <ul class="menu_item">
            <li class="item">
              <a href="#" class="link flex">
                <i class="fa-solid fa-house"></i>
              </a>
            </li>
            <li class="item">
              <a href="../Goals/goals.php" class="link flex">
                <i class="fa-solid fa-coins"></i>
              </a>
            </li>
            <li class="item">
              <a href="../Investment/Investment.html" class="link flex">
                <i class="fa-solid fa-chart-line"></i>
              </a>
            </li>
            <li class="item">
              <a href="../Education/index.html" class="link flex">
                <i class="fa-solid fa-laptop-file"></i>
              </a>
            </li>
            <li class="item">
              <a href="../Expenses/Expense Log.html" class="link flex">
              <i class="fa-solid fa-sack-dollar"></i>

              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Navbar section -->
    <div class="navbar">
      <i class="fa-solid fa-bars" id="sidebar-open"></i>
      <div class="nav_details">
      <div class="date-picker">
    <button id="prev-month" class="arrow-btn">&lt;</button>
    <span id="month" class="date-text"></span>
    <span id="year" class="date-text"></span>
    <button id="next-month" class="arrow-btn">&gt;</button>
</div>

        <div class="nav_icons">
          <!-- Settings icon with hover dropdown -->
          <div class="settings-dropdown">
        <button class="ic settings-btn">
            <i class="fa-solid fa-gear"></i>
        </button>
        <div class="settings-options">
            <ul>
                <li><a href="../profile/profile.php"  >Profile</a></li>
                <li><a href="#" data-section="preferences">Preferences</a></li>
                <li><a href="#" data-section="account">Account</a></li>
                <li><a href="../logout.php" >Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Settings Content Sections -->
    <div  class="settings-content">
        <h2>Profile Settings</h2>
        
        
        
    </div>

    <div id="preferences" class="settings-content">
        <h2>Preferences</h2>
        <form class="settings-form" method="POST">
            <input type="hidden" name="action" value="update_preferences">
            <div class="form-group">
                <label>Theme</label>
                <select name="theme">
                    <option value="light" <?php echo ($userPreferences['theme'] ?? '') === 'light' ? 'selected' : ''; ?>>Light</option>
                    <option value="dark" <?php echo ($userPreferences['theme'] ?? '') === 'dark' ? 'selected' : ''; ?>>Dark</option>
                </select>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="notifications" <?php echo ($userPreferences['notifications'] ?? 0) ? 'checked' : ''; ?>>
                    Enable Notifications
                </label>
            </div>
            <div class="form-group">
                <label>Currency</label>
                <select name="currency">
                    <option value="R" <?php echo ($userPreferences['currency'] ?? '') === 'R' ? 'selected' : ''; ?>>ZAR (R)</option>
                    <option value="$" <?php echo ($userPreferences['currency'] ?? '') === '$' ? 'selected' : ''; ?>>USD ($)</option>
                    <option value="€" <?php echo ($userPreferences['currency'] ?? '') === '€' ? 'selected' : ''; ?>>EUR (€)</option>
                </select>
            </div>
            <button type="submit" class="btn-save">Save Preferences</button>
        </form>
    </div>

    <div id="account" class="settings-content">
        <h2>Account Settings</h2>
        <form class="settings-form" method="POST">
            <input type="hidden" name="action" value="change_password">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="currentPassword" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="newPassword" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirmPassword" required>
            </div>
            <button type="submit" class="btn-save">Change Password</button>
        </form>
    </div>

          <div class="notification-bell">
        <button class="ic bell-btn">
            <i class="fa-solid fa-bell"></i>
            <span class="notification-badge">0</span>
        </button>
        <div class="notifications-box hidden">
            <h4>Notifications</h4>
            <ul id="notifications-list">
                <!-- Notifications will be inserted here -->
            </ul>
        </div>
    </div>

    <audio id="notification-sound" preload="auto">
        <source src="data:audio/mpeg;base64,SUQzBAAAAAABEVRYWFgAAAAtAAADY29tbWVudABCaWdTb3VuZEJhbmsuY29tIC8gTGFTb25vdGhlcXVlLm9yZwBURU5DAAAAHQAAA1N3aXRjaCBQbHVzIMKpIE5DSCBTb2Z0d2FyZQBUSVQyAAAABgAAAzIyMzUAVFNTRQAAAA8AAANMYXZmNTcuODMuMTAwAAAAAAAAAAAAAAD/80DEAAAAA0gAAAAATEFNRTMuMTAwVVVVVVVVVVVVVUxBTUUzLjEwMFVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQsRbAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVf/zQMSkAAADSAAAAABVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV" type="audio/mpeg">
    </audio>

          <!-- Profile dropdown -->
          <div class="dropdown">
            <button class="dropbtn">
              <ul>
                <li><span class="profile">T</span></li>
                <li><span>&or;</span></li>
              </ul>
            </button>
            
            <div class="dropdown-content" style="display:none !important;">
              <a href="#" id="option1">Option 1</a>
              <a href="#" id="option2">Option 2</a>
              <a href="#" id="option3">Option 3</a>
              <a href="#" id="option4">Option 4</a>
            </div>
        
          </div>
        </div>
      </div>
    </div>

    <!-- Main section -->
    <div class="main-section">
      <h2 class="first">My balance</h2>
      <div class="balance-summary-container">
        <!-- Balance section -->
        <div class="balance-section">
          <div class="card">
            <div class="face">
              <h3 class="debit">Debit Card</h3>
              <h3 class="bank"></h3>
              <div class="number">1234 5687 4567 7444</div>
              <h4 class="validfrom">
                <span>VALID <br />FROM</span><span>11/20</span>
              </h4>
              <h4 class="validthru">
                <span>VALID <br />THRU</span><span>11/25</span>
              </h4>
              <h5 class="holder"><?php echo htmlspecialchars($_SESSION['username']); ?></h5>
            </div>
          </div>
          <p>Monthly expense spend</p>
          <input
            type="range"
            id="moneyRange"
            class="range-input"
            min="0"
            max="1000"
            step="1"
            value="500"
            oninput="updateValue(this.value)"
          />
          <div class="value-display">
    <span id="remainingBudget">R0.00</span>
</div>

           <!-- Statistics section -->
          <div class="stats">
            <h3>Statistics</h3>
            <canvas id="myChart" width="400px" height="400" style="width: 380px;display: block;"></canvas>
          </div>
        </div>

        <!-- Summary section -->
        <div class="summary-section">
          <h2 class="second">Summary</h2>
          <div class="summary-container">

          
          <div class="summary-box">
       
        <h4>Total Expenses</h4>
        <div class="months">
            <div class="last-month">
                <p>Last month</p>
                <span>R<?php echo number_format($lastMonthExpenses, 2); ?></span>
            </div>
            <div class="current-month">
                <p>This month</p>
                <span>R<?php echo number_format($currentMonthExpenses, 2); ?></span>
            </div>
        </div>
    </div>

    <div class="summary-box">
    <div class="progress-bar"></div>
    <h4>Total Budget</h4>
    <div class="months">
        <div class="current-month">
            <p>Current Budget</p>
            <span id="moneyValue">R0.00</span>
        </div>
    </div>
</div>

            <!-- Transactions history -->
            <div class="transactions-history" >
              <div class="heading">
                <h2>Transactions History</h2>
                <button class="view all" onclick="scrollToTransactions()">
                  View all
                </button>
              </div>

              
              <div class="transactions" id="transactions-container">

                <!--
                <div class="transaction">
                  <i class="fa-solid fa-bag-shopping"></i>
                  <div class="headings">
                    <div class="first_heading">
                      <h4>Daily</h4>
                      <p>shopping</p>
                    </div>
                    <div class="second-heading">
                      <h4>-R1400.00</h4>
                      <p>12 August 2024</p>
                    </div>
                  </div>
                </div>

                <div class="transaction">
                  <i class="fa-solid fa-gamepad"></i>
                  <div class="headings">
                    <div class="first_heading">
                      <h4>Entertainment game voucher</h4>
                      <p>shopping</p>
                    </div>
                    <div class="second-heading">
                      <h4>-R400.00</h4>
                      <p>3 August 2024</p>
                    </div>
                  </div>
                </div>

                <div class="transaction">
                  <i class="fa-solid fa-briefcase-medical"></i>
                  <div class="headings">
                    <div class="first_heading">
                      <h4>Medical</h4>
                      <p>Medical checkup</p>
                    </div>
                    <div class="second-heading">
                      <h4>-R29000.00</h4>
                      <p>23 June 2024</p>
                    </div>
                  </div>
                </div>

                 -->
              </div>
            </div>
           </div>
            <!-- End of transactions history -->
          </div>
        </div>
        <!-- End of summary section -->
      </div>
    </div>

    <script src="script.js"></script>
    <script src="chart.js"></script>
    <script src="../budget.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
  const budgetLimit = parseFloat(localStorage.getItem("budgetLimit"));
  const remainingBudget = parseFloat(localStorage.getItem("remainingBudget")) || 0;

  if (!isNaN(budgetLimit)) {
   

    // Display the total budget
    document.getElementById("moneyValue").textContent = `R${budgetLimit.toFixed(2)}`;
    
    // Display the remaining budget
    document.getElementById("remainingBudget").textContent = `R${remainingBudget.toFixed(2)}`;
  }
});


    </script>
  </body>
</html>
