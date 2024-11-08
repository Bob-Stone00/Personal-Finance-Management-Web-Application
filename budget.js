// Global variables
let expenses = [];
let budgetLimit = 300;
let pieChart, barChart;
let budgetInputElement, editBudgetBtn;

// Initialize everything when the DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  initializeEventListeners();
  fetchBudget();
  fetchExpenses();
});

// Budget related functions
function fetchBudget() {
  fetch("../transactions.php?type=budget")
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      budgetLimit = parseFloat(data.budget) || 0;
      updateBudgetDisplay();
    })
    .catch((error) => console.error("Error fetching budget:", error));
}

function updateBudget(newBudget) {
  if (isNaN(newBudget) || newBudget < 0) {
    alert("Please enter a valid budget amount");
    return;
  }

  fetch("../transactions.php", {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ budget: newBudget }),
  })
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        budgetLimit = newBudget;
        updateBudgetDisplay();
      } else {
        throw new Error(data.error || "Error updating budget");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(error.message);
    });
}

// Expense related functions
function fetchExpenses() {
  fetch("../transactions.php")
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      expenses = Array.isArray(data) ? data : [];
      updateCharts();
      updateTotalExpenses();
    })
    .catch((error) => console.error("Error:", error));
}

function updateTotalExpenses() {
  const totalExpenses = expenses.reduce(
    (total, expense) => total + (parseFloat(expense.amount) || 0),
    0
  );
  updateBudgetProgress(totalExpenses);
  updateBudgetDisplay();
}

function saveExpense(formData) {
  fetch("../transactions.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        fetchExpenses();
        document.getElementById("expenseForm").reset();
        document.getElementById("date").valueAsDate = new Date();
      } else {
        throw new Error(data.error || "Error saving expense");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(error.message);
    });
}

function deleteExpense(id) {
  if (!confirm("Are you sure you want to delete this expense?")) return;

  fetch(`../transactions.php?id=${id}`, {
    method: "DELETE",
  })
    .then((response) => {
      if (!response.ok) throw new Error("Network response was not ok");
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        fetchExpenses();
      } else {
        throw new Error(data.error || "Error deleting expense");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert(error.message);
    });
}

// UI Update functions
function updateBudgetProgress(totalExpenses) {
  if (typeof totalExpenses !== "number" || typeof budgetLimit !== "number") {
    console.error("Invalid values for budget calculation");
    return;
  }

  const percentUsed = budgetLimit > 0 ? (totalExpenses / budgetLimit) * 100 : 0;
  const budgetBar = document.getElementById("budgetBar");
  const warningAlert = document.getElementById("budgetWarning");
  const dangerAlert = document.getElementById("budgetAlert");

  if (!budgetBar || !warningAlert || !dangerAlert) return;

  // Update progress bar
  budgetBar.style.width = Math.min(percentUsed, 100) + "%";

  // Update progress bar color and show/hide alerts
  if (percentUsed >= 100) {
    budgetBar.className = "budget-bar danger";
    dangerAlert.style.display = "block";
    warningAlert.style.display = "none";
  } else if (percentUsed >= 50) {
    budgetBar.className = "budget-bar warning";
    dangerAlert.style.display = "none";
    warningAlert.style.display = "block";
  } else {
    budgetBar.className = "budget-bar safe";
    dangerAlert.style.display = "none";
    warningAlert.style.display = "none";
  }
}

function updateBudgetDisplay() {
  const totalExpenses = expenses.reduce(
    (total, expense) => total + (parseFloat(expense.amount) || 0),
    0
  );
  const remainingBudget = Math.max(budgetLimit - totalExpenses, 0);

  // Update elements if they exist
  const elements = {
    totalBudget: document.getElementById("totalBudget"),
    remainingBudget: document.getElementById("remainingBudget"),
    moneyValue: document.getElementById("moneyValue"),
    currentBudgetDisplay: document.getElementById("currentBudgetDisplay"),
  };

  if (elements.totalBudget) {
    elements.totalBudget.textContent = budgetLimit.toFixed(2);
  }
  if (elements.remainingBudget) {
    elements.remainingBudget.textContent = remainingBudget.toFixed(2);
  }
  if (elements.moneyValue) {
    elements.moneyValue.textContent = `R${budgetLimit.toFixed(2)}`;
  }
  if (elements.currentBudgetDisplay) {
    elements.currentBudgetDisplay.textContent = `R${budgetLimit.toFixed(2)}`;
  }
}

// Chart related functions
function updateCharts() {
  if (!expenses.length) return;

  updatePieChart();
  updateBarChart();
  updateBudgetDisplay();
}

function updatePieChart() {
  const ctx = document.getElementById("pieChart");
  if (!ctx) return;

  const categoryTotals = expenses.reduce((acc, expense) => {
    const category = expense.icon || "Other";
    acc[category] = (acc[category] || 0) + (parseFloat(expense.amount) || 0);
    return acc;
  }, {});

  const data = {
    labels: Object.keys(categoryTotals),
    datasets: [
      {
        data: Object.values(categoryTotals),
        backgroundColor: [
          "#FF6384",
          "#36A2EB",
          "#FFCE56",
          "#4BC0C0",
          "#9966FF",
        ],
      },
    ],
  };

  if (pieChart) {
    pieChart.destroy();
  }

  pieChart = new Chart(ctx.getContext("2d"), {
    type: "pie",
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: true,
          text: "Expense Distribution by Category",
        },
      },
    },
  });
}

function updateBarChart() {
  const ctx = document.getElementById("barChart");
  if (!ctx) return;

  const dailyTotals = expenses.reduce((acc, expense) => {
    const date = expense.date || "Unknown";
    acc[date] = (acc[date] || 0) + (parseFloat(expense.amount) || 0);
    return acc;
  }, {});

  const sortedDates = Object.keys(dailyTotals).sort();

  const data = {
    labels: sortedDates,
    datasets: [
      {
        label: "Daily Expenses",
        data: sortedDates.map((date) => dailyTotals[date]),
        backgroundColor: "#36A2EB",
      },
    ],
  };

  if (barChart) {
    barChart.destroy();
  }

  barChart = new Chart(ctx.getContext("2d"), {
    type: "bar",
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  });
}

// Event Listeners initialization
function initializeEventListeners() {
  // Initialize DOM element references
  budgetInputElement = document.getElementById("budgetInput");
  editBudgetBtn = document.getElementById("editBudgetBtn");

  if (!budgetInputElement || !editBudgetBtn) {
    console.error("Required budget elements not found");
    return;
  }
  // Budget edit button listener
  editBudgetBtn.addEventListener("click", function () {
    if (budgetInputElement.style.display === "none") {
      budgetInputElement.style.display = "inline-block";
      budgetInputElement.value = budgetLimit;
      this.textContent = "Save Budget";
    } else {
      const newBudget = parseFloat(budgetInputElement.value);
      if (!isNaN(newBudget) && newBudget > 0) {
        updateBudget(newBudget);
        budgetInputElement.style.display = "none";
        this.textContent = "Edit Budget";
      } else {
        alert("Please enter a valid budget amount.");
      }
    }
  });

  // Expense form submit listener
  document
    .getElementById("expenseForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      saveExpense(formData);
    });

  // Set default date to today
  document.getElementById("date").valueAsDate = new Date();

  // Window resize listener for charts
  window.addEventListener("resize", updateCharts);
}
