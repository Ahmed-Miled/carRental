document.addEventListener("DOMContentLoaded", function () {
  const a = document.querySelectorAll(".connecter");

  a.forEach((link) => {
    link.addEventListener("click", function (e) {
      const userType = e.target.value; // Get the value of the clicked button (admin or client)
      alert(userType); // Optional: to check the value

      // Redirect with a query parameter (e.g., ?user=admin or ?user=client)
      window.location.href = `login.html?user=${userType}`;
    });
  });

  const createAccountBtn = document.getElementById("create-account-btn");
  createAccountBtn.addEventListener("click", function () {
    // Default to client role if no button was clicked
    window.location.href = `./signup.html?role=client`; // You can change 'client' based on your logic
  });
});
/*
document.addEventListener("DOMContentLoaded", function () {
  const createAccountBtn = document.getElementById("create-account-btn");

  // Add event listeners for the buttons in the modal
  const buttons = document.querySelectorAll(".connecter");

  buttons.forEach((button) => {
    button.addEventListener("click", function () {
      const role = this.value; // Either 'client' or 'admin'
      // Redirect to signup page with the role as a query parameter
      window.location.href = `./signup.html?role=${role}`;
    });
  });

  createAccountBtn.addEventListener("click", function () {
    // Default to client role if no button was clicked
    window.location.href = `./signup.html?role=client`; // You can change 'client' based on your logic
  });
});
*/
