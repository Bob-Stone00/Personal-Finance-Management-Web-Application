

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Goals</title>
    <link rel="stylesheet" href="goal.css" />
    <link rel="stylesheet" href="../Dashboard/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="java.js"></script>
    <style>
        button {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}
        /* Edit button */
.delete-goal {
  background-color: #dc3545;
  color: white;
}

button.edit:hover {
 
  background-color: #c82333;
}

/* Delete button */
.edit-goal {
 
  background-color: #28a745;
  color: white;
}

button.delete:hover {
  background-color: #218838;
}
    </style>
  </head>
  <body>

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
                    <a href="../Dashboard/index.php" class="link flex">
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

    <div class="dashboard">
      <header class="dashboard-header">
        <h1>Financial Goals</h1>
        <div class="controls">
          <input type="text" id="searchInput" placeholder="Search..." />
          <select id="sortSelect">
            <option value="value">Sort by Amount</option>
            <option value="percentage">Sort by Percentage</option>
          </select>
          <button id="addMetricBtn">Add Goal</button>
        </div>
      </header>

      <div id="metricsContainer" class="metrics-container">
        <!-- Goals will be dynamically added here -->
      </div>
    </div>

    <!-- Modal for Adding/Editing Goal -->
    <div id="goalModal" class="modal">
      <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">Add New Financial Goal</h2>
        <form id="goalForm">
          <input type="hidden" id="goalId">
          <input type="text" id="goalName" placeholder="Goal Name" required />
          <input type="number" id="goalAmount" placeholder="Goal Amount" required />
          <input type="number" id="currentAmount" placeholder="Current Amount" required />
          <input type="text" id="assignedTo" placeholder="Assigned To" required />
          <button type="submit">Save Goal</button>
        </form>
      </div>
    </div>
  </body>
</html>

<script>
$(document).ready(function() {
    // Load goals on page load
    loadGoals();

    // Search Functionality
    $("#searchInput").on("input", function() {
        const filter = $(this).val().toLowerCase();
        $(".metric-card").each(function() {
            const title = $(this).find("h2").text().toLowerCase();
            $(this).toggle(title.includes(filter));
        });
    });

    // Sort Functionality
    $("#sortSelect").on("change", function() {
        const sortValue = $(this).val();
        const $container = $("#metricsContainer");
        const $cards = $container.children(".metric-card").get();
        $cards.sort(function(a, b) {
            const valueA = parseFloat($(a).find(`.${sortValue}`).text().replace(/[^\d.-]/g, ""));
            const valueB = parseFloat($(b).find(`.${sortValue}`).text().replace(/[^\d.-]/g, ""));
            return valueB - valueA; // Descending order
        });
        $container.empty().append($cards);
    });

    // Add Goal Modal
    $("#addMetricBtn").click(function() {
        $("#modalTitle").text("Add New Financial Goal");
        $("#goalId").val("");
        $("#goalForm")[0].reset();
        $("#goalModal").show();
    });

    $(".close").click(function() {
        $("#goalModal").hide();
    });

    $(window).click(function(event) {
        if (event.target == $("#goalModal")[0]) {
            $("#goalModal").hide();
        }
    });

    // Handle form submission
    $("#goalForm").submit(function(e) {
        e.preventDefault();
        const goalId = $("#goalId").val();
        const goalData = {
            goal_name: $("#goalName").val(),
            goal_amount: $("#goalAmount").val(),
            current_amount: $("#currentAmount").val(),
            assigned_to: $("#assignedTo").val()
        };

        if (goalId) {
            // Update existing goal
            updateGoal(goalId, goalData);
        } else {
            // Add new goal
            addGoal(goalData);
        }
    });

    // Load goals from the server
    function loadGoals() {
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: { action: 'getAll' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $("#metricsContainer").empty();
                    response.goals.forEach(function(goal) {
                        addGoalCard(goal);
                    });
                }
            }
        });
    }

    // Add a new goal
    function addGoal(goalData) {
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: { action: 'add', ...goalData },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $("#goalModal").hide();
                    loadGoals();
                } else {
                    alert(response.message);
                }
            }
        });
    }

    // Update an existing goal
    function updateGoal(id, goalData) {
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: { action: 'update', id: id, ...goalData },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $("#goalModal").hide();
                    loadGoals();
                } else {
                    alert(response.message);
                }
            }
        });
    }

    // Delete a goal
    function deleteGoal(id) {
        if (confirm("Are you sure you want to delete this goal?")) {
            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        loadGoals();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    }

    // Add a goal card to the dashboard
    function addGoalCard(goal) {
        const percentage = (goal.current_amount / goal.goal_amount) * 100;
        const statusClass = percentage >= 100 ? 'green' : (percentage >= 50 ? 'yellow' : 'red');
        const card = `
            <div class="metric-card ${statusClass}" data-id="${goal.id}">
                <div class="top-bar">
                    <h2>${goal.goal_name}</h2>
                    <span class="star">★</span>
                </div>
                <p class="value">R${parseFloat(goal.goal_amount).toFixed(2)}</p>
                <p class="percentage">${percentage.toFixed(2)}%</p>
                <progress value="${percentage}" max="100"></progress>
                <p class="status ${statusClass}">${goal.status}</p>
                <p class="assigned" style="display:none;">${goal.assigned_to}</p>
                <div class="card-actions">
                    <button class="edit-goal">Edit</button>
                    <button class="delete-goal">Delete</button>
                </div>
            </div>
        `;
        $("#metricsContainer").append(card);
    }

    // Event delegation for edit and delete buttons
    $("#metricsContainer").on("click", ".edit-goal", function() {
        const $card = $(this).closest(".metric-card");
        const goalId = $card.data("id");
        const goalName = $card.find("h2").text();
        const goalAmount = parseFloat($card.find(".value").text().replace("$", ""));
        const currentAmount = (parseFloat($card.find(".percentage").text()) / 100) * goalAmount;
        const assignedTo = $card.find(".assigned").text();

        $("#modalTitle").text("Edit Financial Goal");
        $("#goalId").val(goalId);
        $("#goalName").val(goalName);
        $("#goalAmount").val(goalAmount);
        $("#currentAmount").val(currentAmount);
        $("#assignedTo").val(assignedTo);
        $("#goalModal").show();
    });

    $("#metricsContainer").on("click", ".delete-goal", function() {
        const goalId = $(this).closest(".metric-card").data("id");
        deleteGoal(goalId);
    });
});
</script>
