function previewImage(event) {
  const file = event.target.files[0]; // Get the selected file
  const reader = new FileReader(); // Create a FileReader object

  reader.onload = function (e) {
    const profilePic = document.getElementById("profilePic"); // Get the profile picture img element
    profilePic.src = e.target.result; // Set the src to the file's data URL
  };

  if (file) {
    reader.readAsDataURL(file); // Read the file as a data URL
  }
}

// Get the button and form elements
const toggleButton = document.getElementById("toggleButton");
const profileForm = document.getElementById("profileForm");

// Add an event listener to the button
toggleButton.addEventListener("click", function () {
  // Toggle the display property of the form
  if (
    profileForm.style.display === "none" ||
    profileForm.style.display === ""
  ) {
    profileForm.style.display = "block";
    toggleButton.textContent = "Hide Form";
  } else {
    profileForm.style.display = "none";
    toggleButton.textContent = "Show Form";
  }
});

function logoutUser() {
  // Step 1: Clear any stored session or login information
  sessionStorage.clear(); // Clears session storage
  localStorage.clear(); // Clears local storage

  // Step 2: Redirect to the login page
  window.location.replace("../logout.php"); // Replace with the correct login page path

  // Step 3: Push a blank state to prevent going back to the app pages
  window.history.pushState(null, "", window.location.href);
  window.onpopstate = function () {
    window.history.pushState(null, "", window.location.href); // Pushes a blank state if the user tries to go back
  };

  // Step 4: Redirect to a dummy page (like an empty HTML page) before the login page to clear history
  setTimeout(() => {
    window.location.replace("../logout.php");
  }, 10);
}
