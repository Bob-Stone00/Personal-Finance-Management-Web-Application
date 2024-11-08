// Search Functionality
const searchInput = document.getElementById("searchInput");
searchInput.addEventListener("input", function () {
  const filter = searchInput.value.toLowerCase();
  const metricCards = document.querySelectorAll(".metric-card");
  metricCards.forEach((card) => {
    const title = card.querySelector("h2").textContent.toLowerCase();
    if (title.includes(filter)) {
      card.style.display = "";
    } else {
      card.style.display = "none";
    }
  });
});

// Sort Functionality
const sortSelect = document.getElementById("sortSelect");
sortSelect.addEventListener("change", function () {
  const metricsContainer = document.getElementById("metricsContainer");
  const metricCards = Array.from(
    metricsContainer.getElementsByClassName("metric-card")
  );
  const sortValue = sortSelect.value;

  const sortedCards = metricCards.sort((a, b) => {
    const valueA = parseInt(
      a.querySelector(`.${sortValue}`).textContent.replace(/[^\d]/g, ""),
      10
    );
    const valueB = parseInt(
      b.querySelector(`.${sortValue}`).textContent.replace(/[^\d]/g, ""),
      10
    );
    return valueB - valueA; // Descending order
  });

  metricsContainer.innerHTML = "";
  sortedCards.forEach((card) => metricsContainer.appendChild(card));
});

// Add Metric Modal
const addMetricBtn = document.getElementById("addMetricBtn");
const addMetricModal = document.getElementById("addMetricModal");
const closeBtn = document.getElementsByClassName("close")[0];
addMetricBtn.addEventListener("click", function () {
  addMetricModal.style.display = "block";
});
closeBtn.addEventListener("click", function () {
  addMetricModal.style.display = "none";
});
window.addEventListener("click", function (event) {
  if (event.target == addMetricModal) {
    addMetricModal.style.display = "none";
  }
});

// Adding a New Metric
const addMetricForm = document.getElementById("addMetricForm");
addMetricForm.addEventListener("submit", function (event) {
  event.preventDefault();
  const metricName = document.getElementById("metricName").value;
  const metricValue = document.getElementById("metricValue").value;
  const metricPercentage = document.getElementById("metricPercentage").value;

  const newMetricCard = document.createElement("div");
  newMetricCard.classList.add("metric-card", "green");
  newMetricCard.innerHTML = `
    <div class="top-bar">
      <h2>${metricName}</h2>
      <span class="star">★</span>
    </div>
    <p class="value">${metricValue}</p>
    <p class="percentage">${metricPercentage}%</p>
    <progress value="${metricPercentage}" max="100"></progress>
    <p class="status green">New metric added</p>
    <p class="assigned">Assigned to You</p>
  `;
  document.getElementById("metricsContainer").appendChild(newMetricCard);

  // Close modal
  addMetricModal.style.display = "none";
  addMetricForm.reset();
});

// Create New Goal
const createNewGoal = document.getElementById("createNewGoal");
createNewGoal.addEventListener("click", function () {
  alert("Create a new goal functionality can be developed here.");
});
$(document).ready(function() {
  // Load goals on page load
  loadGoals();

  // Toggle sidebar
  $("#sidebar-close, #addMetricBtn").click(function() {
      $(".sidebar").toggleClass("show-sidebar");
  });

  // Open Add Goal Modal
  $("#addMetricBtn").click(function() {
      $("#modalTitle").text("Add New Financial Goal");
      $("#goalId").val("");
      $("#goalForm")[0].reset();
      $("#goalModal").fadeIn(); // Use fadeIn for smooth appearance
  });

  // Close Modal
  $(".close, .modal").click(function(event) {
      if (event.target == $(".modal")[0] || $(this).hasClass("close")) {
          $("#goalModal").fadeOut();
      }
  });

 
});
$(document).ready(function() {
  // Toggle sidebar open and close on button click
  $("#sidebar-toggle").click(function() {
      $(".sidebar").toggleClass("show-sidebar");
  });

  // Close the sidebar when clicking outside (for mobile experience)
  $(document).click(function(event) {
      if (!$(event.target).closest(".sidebar, #sidebar-toggle").length) {
          $(".sidebar").removeClass("show-sidebar");
      }
  });

  // Other code remains as is (load goals, modal events, etc.)
});
