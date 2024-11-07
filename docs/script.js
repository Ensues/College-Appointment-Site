// Log In Behaviors
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const loginBtn = document.getElementById('login-btn');

emailInput.addEventListener('input', checkInputs);
passwordInput.addEventListener('input', checkInputs);
loginBtn.addEventListener('click', login);

// Log In Functions
function checkInputs() {
  const emailValue = emailInput.value.trim();
  const passwordValue = passwordInput.value.trim();

  if (emailValue !== '' && passwordValue !== '') {
    loginBtn.disabled = false;
  } else {
    loginBtn.disabled = true;
  }
}

async function login() {
  console.log('Login button clicked!');

  const email = emailInput.value;
  const password = passwordInput.value;

  try {
    // Simulate a successful login response
    const response = {
      success: true,
      message: 'Login successful!',
    };

    console.log('Login successful:', response);
    // Redirect to another page or perform other actions
    window.location.href = 'library.html'; // Example redirect
  } catch (error) {
    console.error('There was a problem with the login:', error);
    alert('An error occurred. Please try again later.'); // Show a generic error message
  }
}