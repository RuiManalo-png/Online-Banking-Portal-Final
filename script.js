// Function to show the logout modal
function openLogoutModal() {
    document.getElementById("logoutModal").style.display = "flex";
  }
  
  // Function to hide the logout modal
  function closeLogoutModal() {
    document.getElementById("logoutModal").style.display = "none";
  }
  
  // Function to confirm logout and redirect
  function confirmLogout() {
    window.location.href = "index.html"; // Update this URL if needed
  }
  // Handle Login Form Submission
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent page reload
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        // Simple validation (you can expand this as needed)
        if (email && password) {
            console.log('Login Successful');
            console.log('Email:', email);
            console.log('Password:', password);
            alert('Login Successful!'); // You can replace this with actual login logic
            // Close the modal after successful login
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.hide();
        } else {
            alert('Please fill in all fields.');
        }
    });

    // Handle Sign Up Form Submission
    document.getElementById('signupForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent page reload
        const name = document.getElementById('signupName').value;
        const email = document.getElementById('signupEmail').value;
        const password = document.getElementById('signupPassword').value;

        // Simple validation (you can expand this as needed)
        if (name && email && password) {
            console.log('Sign Up Successful');
            console.log('Name:', name);
            console.log('Email:', email);
            console.log('Password:', password);
            alert('Sign Up Successful!'); // You can replace this with actual sign up logic
            // Close the modal after successful sign up
            var signupModal = new bootstrap.Modal(document.getElementById('signupModal'));
            signupModal.hide();
        } else {
            alert('Please fill in all fields.');
        }
    });

 
            