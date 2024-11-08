// Sidebar

const sidebar = document.querySelector(".sidebar");
const sidebarOpenBtn = document.getElementById("sidebar-open");
const sidebarCloseBtn = document.getElementById("sidebar-close");

sidebarOpenBtn.addEventListener("click", () => {
  sidebar.classList.add("show-sidebar");
});

sidebarCloseBtn.addEventListener("click", () => {
  sidebar.classList.remove("show-sidebar");
});

//scroll button
function scrollToTransactions() {
  // Scroll smoothly to the transactions section
  const transactionSection = document.querySelector(".transactions-history");
  window.location.href = "../Expenseshistory/Expenseshistory.html";

  transactionSection.scrollIntoView({ behavior: "smooth" });
}

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

const months = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "May",
  "Jun",
  "Jul",
  "Aug",
  "Sep",
  "Oct",
  "Nov",
  "Dec",
];
const monthElement = document.getElementById("month");
const yearElement = document.getElementById("year");

// Function to update the displayed month and year
function updateDate() {
  monthElement.textContent = months[currentMonth];
  yearElement.textContent = currentYear;
}

// functionality for Previous month button
document.getElementById("prev-month").addEventListener("click", () => {
  if (currentMonth === 0) {
    currentMonth = 11;
    currentYear--;
  } else {
    currentMonth--;
  }
  updateDate();
});

//functionality for the next month button
document.getElementById("next-month").addEventListener("click", () => {
  if (currentMonth === 11) {
    currentMonth = 0;
    currentYear++;
  } else {
    currentMonth++;
  }
  updateDate();
});

const dropBtn = document.querySelector(".dropbtn");
const dropdownContent = document.querySelector(".dropdown-content");

// Toggle dropdown visibility on button click
dropBtn.addEventListener("click", () => {
  dropdownContent.classList.toggle("show");
});

// Close dropdown if clicked outside
window.addEventListener("click", (e) => {
  if (!e.target.matches(".dropbtn")) {
    dropdownContent.classList.remove("show");
  }
});

// Optionally, add functionality to handle option clicks
document.getElementById("option1").addEventListener("click", () => {
  alert("Option 1 selected");
});

document.getElementById("option2").addEventListener("click", () => {
  alert("Option 2 selected");
});

document.getElementById("option3").addEventListener("click", () => {
  alert("Option 3 selected");
});

document.getElementById("option4").addEventListener("click", () => {
  alert("Option 4 selected");
});

//  notification box
const bellBtn = document.querySelector(".bell-btn");
const notificationsBox = document.querySelector(".notifications-box");

bellBtn.addEventListener("click", () => {
  notificationsBox.classList.toggle("visible");
});

document.addEventListener("click", (event) => {
  if (
    !bellBtn.contains(event.target) &&
    !notificationsBox.contains(event.target)
  ) {
    notificationsBox.classList.remove("visible");
  }
});

//chart code

/*
document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("myChart").getContext("2d");

  const myChart = new Chart(ctx, {
    type: "line",
    data: {
      labels: ["January", "February", "March", "April", "May", "June", "July"],
      datasets: [
        {
          label: "Daily Transactions",
          data: [12, 19, 3, 5, 2, 3, 30],
          backgroundColor: [
            "rgba(255, 99, 132, 0.2)",
            "rgba(54, 162, 235, 0.2)",
            "rgba(255, 206, 86, 0.2)",
            "rgba(75, 192, 192, 0.2)",
            "rgba(153, 102, 255, 0.2)",
            "rgba(255, 159, 64, 0.2)",
            "rgba(255, 159, 64, 0.2)",
          ],
          borderColor: [
            "rgba(255, 99, 132, 1)",
            "rgba(54, 162, 235, 1)",
            "rgba(255, 206, 86, 1)",
            "rgba(75, 192, 192, 1)",
            "rgba(153, 102, 255, 1)",
            "rgba(255, 159, 64, 1)",
            "rgba(255, 159, 64, 1)",
          ],
          borderWidth: 2,
        },
      ],
    },
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
});
*/

document.addEventListener("DOMContentLoaded", function () {
  const ctx = document.getElementById("myChart").getContext("2d");

  // Fetch data from the PHP API
  fetch("../transactions.php")
    .then((response) => response.json())
    .then((data) => {
      // Sort data by date in ascending order
      data.sort((a, b) => new Date(a.date) - new Date(b.date));

      // Map sorted data to get dates and amounts for the chart
      const labels = data.map((transaction) => transaction.date);
      const amounts = data.map((transaction) => transaction.amount);

      const myChart = new Chart(ctx, {
        type: "line",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Daily Transactions",
              data: amounts,
              backgroundColor: "rgba(75, 192, 192, 0.2)",
              borderColor: "rgba(75, 192, 192, 1)",
              borderWidth: 3,
            },
          ],
        },
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
    })
    .catch((error) => console.error("Error fetching data:", error));
});

function formatDate(dateString) {
  const options = { year: "numeric", month: "long", day: "numeric" };
  return new Date(dateString).toLocaleDateString(undefined, options);
}

// Fetch transactions from the PHP backend

async function fetchTransactions() {
  try {
    const response = await fetch("../transactions.php");
    const transactions = await response.json();
    renderTransactions(transactions);
  } catch (error) {
    console.error("Error fetching transactions:", error);
  }
}

// Function to render transactions dynamically
function renderTransactions(transactions) {
  const transactionsContainer = document.getElementById(
    "transactions-container"
  );
  //transactionsContainer.innerHTML = ""; // Clear existing content

  transactions.forEach((transaction) => {
    const transactionElement = document.createElement("div");
    transactionElement.className = "transaction";
    transactionElement.innerHTML = `
        <i class="fa-solid fa-bag-shopping"></i> 
        <div class="headings">
        <div class="first_heading">
          <h4>${transaction.title}</h4>
          <p>${transaction.description}</p>
        </div>
        <div class="second-heading">
          <h4>R${parseFloat(transaction.amount).toFixed(2)}</h4>
          <p>${formatDate(transaction.date)}</p>
        </div>
        </div>

      </div>
    `;

    transactionsContainer.appendChild(transactionElement);
  });
}

// Call the fetchTransactions function when the page loads
document.addEventListener("DOMContentLoaded", fetchTransactions);

document.addEventListener("DOMContentLoaded", function () {
  const bellBtn = document.querySelector(".bell-btn");
  const notificationsBox = document.querySelector(".notifications-box");
  const notificationsList = document.getElementById("notifications-list");
  const notificationBadge = document.querySelector(".notification-badge");
  const notificationSound = document.getElementById("notification-sound");
  let unreadCount = 0;

  // Toggle notifications box
  bellBtn.addEventListener("click", function () {
    notificationsBox.classList.toggle("hidden");
    if (!notificationsBox.classList.contains("hidden")) {
      unreadCount = 0;
      notificationBadge.style.display = "none";
    }
  });

  // Close notifications box when clicking outside
  document.addEventListener("click", function (event) {
    if (
      !bellBtn.contains(event.target) &&
      !notificationsBox.contains(event.target)
    ) {
      notificationsBox.classList.add("hidden");
    }
  });

  // Function to add a new notification
  function addNotification(message) {
    const li = document.createElement("li");
    li.textContent = message;
    notificationsList.insertBefore(li, notificationsList.firstChild);

    // Update badge
    unreadCount++;
    notificationBadge.textContent = unreadCount;
    notificationBadge.style.display = "block";

    // Animate bell
    bellBtn.classList.add("bell-shake");
    setTimeout(() => bellBtn.classList.remove("bell-shake"), 1000);

    // Play sound
    notificationSound
      .play()
      .catch((error) => console.log("Sound play failed:", error));
  }

  // Check for new notifications periodically
  function checkNotifications() {
    fetch("notifications.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.notifications && data.notifications.length > 0) {
          data.notifications.forEach((notification) => {
            addNotification(notification.message);
          });
        }
      })
      .catch((error) => console.error("Error checking notifications:", error));
  }

  // Check for notifications every 30 seconds
  setInterval(checkNotifications, 10000);
  // Initial check
  checkNotifications();
});

document.addEventListener("DOMContentLoaded", function () {
  const settingsBtn = document.querySelector(".settings-btn");
  const settingsOptions = document.querySelector(".settings-options");
  const settingsSections = document.querySelectorAll(".settings-content");

  // Toggle settings dropdown
  settingsBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    settingsOptions.style.display =
      settingsOptions.style.display === "block" ? "none" : "block";
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (
      !settingsBtn.contains(e.target) &&
      !settingsOptions.contains(e.target)
    ) {
      settingsOptions.style.display = "none";
    }
  });

  // Handle settings navigation
  document.querySelectorAll(".settings-options a").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();

      // Only proceed if the link has a data-section attribute
      const section = this.getAttribute("data-section");
      if (!section) {
        // If no data-section, assume it's the Logout link and allow it to proceed
        window.location.href = this.getAttribute("href");
        return;
      }

      // Hide all sections
      settingsSections.forEach((sec) => {
        sec.classList.remove("active");
      });

      // Show selected section if it exists
      const targetSection = document.getElementById(section);
      if (targetSection) {
        targetSection.classList.add("active");
      } else {
        console.error(`Section with ID ${section} not found`);
      }

      // Close dropdown
      settingsOptions.style.display = "none";
    });
  });
});

// Function to set the current date in the date picker
function setCurrentDate() {
  const date = new Date();
  const monthNames = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "May",
    "Jun",
    "Jul",
    "Aug",
    "Sep",
    "Oct",
    "Nov",
    "Dec",
  ];

  // Get current month and year
  const currentMonth = monthNames[date.getMonth()];
  const currentYear = date.getFullYear();

  // Set the month and year in the HTML
  document.getElementById("month").textContent = currentMonth;
  document.getElementById("year").textContent = currentYear;
}

// Call the function to set the date on page load
setCurrentDate();

const profile = document.getElementById("profileY");

profile.addEventListener("click", () => {
  window.location.href = "../profile/profile.html";
});
