// Execute all code when DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
  // Navbar scroll effect
  window.addEventListener("scroll", function () {
    const navbar = document.querySelector(".navbar");
    if (window.scrollY > 100) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  });

  // Mobile menu
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");

  if (hamburger && navLinks) {
    hamburger.addEventListener("click", function () {
      navLinks.classList.toggle("active");
      hamburger.classList.toggle("active");
      hamburger.textContent = hamburger.classList.contains("active")
        ? "✕"
        : "☰";
    });

    // Close mobile menu when clicking on a nav link
    document.querySelectorAll(".nav-links a").forEach((link) => {
      link.addEventListener("click", function () {
        navLinks.classList.remove("active");
        hamburger.classList.remove("active");
        hamburger.textContent = "☰";
      });
    });
  }

  // Active link highlighting
  const sections = document.querySelectorAll("section");
  const navItems = document.querySelectorAll(".nav-links a");

  function highlightNav() {
    let scrollPosition = window.scrollY;

    sections.forEach((section) => {
      const sectionTop = section.offsetTop - 100;
      const sectionHeight = section.clientHeight;
      const sectionId = section.getAttribute("id");

      if (
        scrollPosition >= sectionTop &&
        scrollPosition < sectionTop + sectionHeight
      ) {
        navItems.forEach((item) => {
          item.classList.remove("active");
          if (item.getAttribute("href") === "#" + sectionId) {
            item.classList.add("active");
          }
        });
      }
    });
  }

  window.addEventListener("scroll", highlightNav);

  // Initial call to highlight the correct nav item
  highlightNav();

  // Contact form submission with AJAX
  const contactForm = document.getElementById("contact-form");

  if (contactForm) {
    contactForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Get form data
      const formData = new FormData(contactForm);
      const submitBtn = contactForm.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.textContent;

      // Disable submit button and show loading state
      submitBtn.disabled = true;
      submitBtn.textContent = "Sending...";

      // Send AJAX request
      fetch("contact_handler.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            // Show success message
            showMessage(data.message, "success");
            // Reset form
            contactForm.reset();
          } else {
            // Show error message
            showMessage(data.message, "error");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showMessage(
            "Sorry, there was an error sending your message. Please try again later.",
            "error"
          );
        })
        .finally(() => {
          // Re-enable submit button
          submitBtn.disabled = false;
          submitBtn.textContent = originalBtnText;
        });
    });

    // Function to show messages
    function showMessage(message, type) {
      // Remove any existing message
      const existingMessage = document.querySelector(".form-message");
      if (existingMessage) {
        existingMessage.remove();
      }

      // Create message element
      const messageDiv = document.createElement("div");
      messageDiv.className = `form-message ${type}`;
      messageDiv.textContent = message;

      // Style the message
      messageDiv.style.cssText = `
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            font-weight: 500;
            ${
              type === "success"
                ? "background: #d4edda; color: #155724; border: 1px solid #c3e6cb;"
                : "background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;"
            }
        `;

      // Insert message before the form
      contactForm.parentNode.insertBefore(messageDiv, contactForm);

      // Auto-remove message after 5 seconds
      setTimeout(() => {
        if (messageDiv && messageDiv.parentNode) {
          messageDiv.remove();
        }
      }, 5000);
    }
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();

      const targetId = this.getAttribute("href");
      if (targetId === "#") return;

      const targetElement = document.querySelector(targetId);

      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 70, // Offset for fixed navbar
          behavior: "smooth",
        });
      }
    });
  });
});
