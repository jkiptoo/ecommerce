// Modern JavaScript functions
document.addEventListener('DOMContentLoaded', function() {
  // Initialize any needed functionality
});

// Trim function with modern JavaScript
function trim(inputString) {
  if (typeof inputString !== "string") { 
      return inputString; 
  }
  return inputString.trim().replace(/\s+/g, ' ');
}

// Form validation function
function validateForm(formId, errorElementId) {
  const form = document.getElementById(formId);
  const errorElement = document.getElementById(errorElementId);
  
  if (!form || !errorElement) return false;
  
  let isValid = true;
  const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
  
  inputs.forEach(input => {
      if (!input.value.trim()) {
          errorElement.textContent = `Please fill in the ${input.name} field.`;
          input.focus();
          isValid = false;
          return;
      }
      
      // Add additional validation as needed
      if (input.type === 'email' && !validateEmail(input.value)) {
          errorElement.textContent = 'Please enter a valid email address.';
          input.focus();
          isValid = false;
          return;
      }
  });
  
  return isValid;
}

// Email validation
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

// Form submission with fetch API (modern alternative to AJAX)
async function submitForm(formId, endpoint) {
  const form = document.getElementById(formId);
  if (!form) return false;
  
  const formData = new FormData(form);
  
  try {
      const response = await fetch(endpoint, {
          method: 'POST',
          body: formData
      });
      
      if (!response.ok) {
          throw new Error('Network response was not ok');
      }
      
      return await response.json();
  } catch (error) {
      console.error('Error:', error);
      return false;
  }
}

// Export functions if using modules
// export { trim, validateForm, validateEmail, submitForm };