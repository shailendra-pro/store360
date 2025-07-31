// Clean Admin Theme JavaScript

document.addEventListener("DOMContentLoaded", () => {
  // Import Bootstrap
  const bootstrap = window.bootstrap

  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map((tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl))

  // Sidebar navigation active state
  const navLinks = document.querySelectorAll(".sidebar-nav .nav-link")

  navLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      // Remove active class from all links
      navLinks.forEach((l) => l.classList.remove("active"))

      // Add active class to clicked link (if it's not a collapse trigger)
      if (!this.hasAttribute("data-bs-toggle")) {
        this.classList.add("active")
      }
    })
  })

  // Auto-close mobile sidebar when clicking on a link
  const sidebarLinks = document.querySelectorAll("#sidebar .nav-link")
  const sidebar = document.getElementById("sidebar")

  if (sidebar) {
    sidebarLinks.forEach((link) => {
      link.addEventListener("click", () => {
        if (window.innerWidth < 992) {
          const bsOffcanvas = bootstrap.Offcanvas.getInstance(sidebar)
          if (bsOffcanvas) {
            bsOffcanvas.hide()
          }
        }
      })
    })
  }

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()
      const target = document.querySelector(this.getAttribute("href"))
      if (target) {
        target.scrollIntoView({
          behavior: "smooth",
          block: "start",
        })
      }
    })
  })

  // Add loading animation to buttons on click
  document.querySelectorAll(".btn").forEach((button) => {
    button.addEventListener("click", function () {
      if (!this.classList.contains("loading")) {
        this.classList.add("loading")
        setTimeout(() => {
          this.classList.remove("loading")
        }, 2000)
      }
    })
  })

  // Dynamic stats counter animation
  function animateCounter(element, target, duration = 2000) {
    let start = 0
    const increment = target / (duration / 16)

    function updateCounter() {
      start += increment
      if (start < target) {
        element.textContent = Math.floor(start).toLocaleString()
        requestAnimationFrame(updateCounter)
      } else {
        element.textContent = target.toLocaleString()
      }
    }

    updateCounter()
  }

  // Animate stats on page load
  const statsElements = document.querySelectorAll(".card-body h4")
  statsElements.forEach((element) => {
    const text = element.textContent.replace(/[^0-9]/g, "")
    const number = Number.parseInt(text)
    if (number) {
      element.textContent = "0"
      setTimeout(() => {
        animateCounter(element, number)
      }, 500)
    }
  })

  // Search functionality (basic implementation)
  const searchInput = document.querySelector('input[placeholder*="search"]')
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const searchTerm = this.value.toLowerCase()
      const menuItems = document.querySelectorAll(".sidebar-nav .nav-link")

      menuItems.forEach((item) => {
        const text = item.textContent.toLowerCase()
        const listItem = item.closest(".nav-item")

        if (text.includes(searchTerm) || searchTerm === "") {
          listItem.style.display = "block"
        } else {
          listItem.style.display = "none"
        }
      })
    })
  }

  // Theme switcher (if needed)
  function toggleTheme() {
    document.body.classList.toggle("dark-theme")
    localStorage.setItem("theme", document.body.classList.contains("dark-theme") ? "dark" : "light")
  }

  // Load saved theme
  const savedTheme = localStorage.getItem("theme")
  if (savedTheme === "dark") {
    document.body.classList.add("dark-theme")
  }

  // Notification system
  function showNotification(message, type = "info") {
    const notification = document.createElement("div")
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`
    notification.style.cssText = "top: 100px; right: 20px; z-index: 1050; min-width: 300px;"
    notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `

    document.body.appendChild(notification)

    setTimeout(() => {
      notification.remove()
    }, 5000)
  }

  // Form validation
//   const forms = document.querySelectorAll("form")
//   forms.forEach((form) => {
//     form.addEventListener("submit", (e) => {
//       e.preventDefault()

//       const inputs = form.querySelectorAll("input[required]")
//       let isValid = true

//       inputs.forEach((input) => {
//         if (!input.value.trim()) {
//           input.classList.add("is-invalid")
//           isValid = false
//         } else {
//           input.classList.remove("is-invalid")
//           input.classList.add("is-valid")
//         }
//       })

//     //   if (isValid) {
//     //     showNotification("Form submitted successfully!", "success")
//     //   } else {
//     //     showNotification("Please fill in all required fields.", "danger")
//     //   }
//     })
//   })

  // Auto-hide alerts
  setTimeout(() => {
    const alerts = document.querySelectorAll(".alert")
    alerts.forEach((alert) => {
      if (alert.classList.contains("auto-hide")) {
        alert.remove()
      }
    })
  }, 5000)

  // Responsive table wrapper
  const tables = document.querySelectorAll("table")
  tables.forEach((table) => {
    if (!table.parentElement.classList.contains("table-responsive")) {
      const wrapper = document.createElement("div")
      wrapper.className = "table-responsive"
      table.parentNode.insertBefore(wrapper, table)
      wrapper.appendChild(table)
    }
  })

  // Initialize charts (placeholder for chart library integration)
  function initializeCharts() {
    const chartPlaceholders = document.querySelectorAll(".chart-placeholder")
    chartPlaceholders.forEach((placeholder) => {
      // This is where you would initialize your preferred chart library
      // Example: Chart.js, D3.js, etc.
      console.log("Chart placeholder found:", placeholder)
    })
  }

  initializeCharts()
})

// Utility functions
const AdminUtils = {
  // Format currency
  formatCurrency: (amount) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(amount)
  },

  // Format date
  formatDate: (date) => {
    return new Intl.DateTimeFormat("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
    }).format(new Date(date))
  },

  // Debounce function
  debounce: (func, wait) => {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  },

  // Local storage helpers
  storage: {
    set: (key, value) => {
      try {
        localStorage.setItem(key, JSON.stringify(value))
      } catch (e) {
        console.error("Error saving to localStorage:", e)
      }
    },
    get: (key) => {
      try {
        const item = localStorage.getItem(key)
        return item ? JSON.parse(item) : null
      } catch (e) {
        console.error("Error reading from localStorage:", e)
        return null
      }
    },
    remove: (key) => {
      try {
        localStorage.removeItem(key)
      } catch (e) {
        console.error("Error removing from localStorage:", e)
      }
    },
  },
}

// Export for use in other scripts
window.AdminUtils = AdminUtils
