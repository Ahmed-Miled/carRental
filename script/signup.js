// Function to get query parameters from the URL
document.addEventListener("DOMContentLoaded", function () {
  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  // Get the 'user' parameter and display the appropriate message
  const userType = getQueryParam("user");
  const messageElement = document.getElementById("message");

  if (userType === "admin") {
    messageElement.textContent = "You are logged in as Admin";
  } else if (userType === "client") {
    messageElement.textContent = "You are logged in as Client";
  } else {
    messageElement.textContent = "Unknown user type";
  }
});
