
document.addEventListener('DOMContentLoaded', function() {
  // ===== MOBILE MENU TOGGLE =====
  const menuToggle = document.querySelector('.menu-toggle');
  const navLinksContainer = document.querySelector('.nav-links');
  
  menuToggle.addEventListener('click', function() {
    this.classList.toggle('active');
    navLinksContainer.classList.toggle('active');
  });
  
  // ===== ACTIVE LINK MANAGEMENT =====
  
    const navLinks = document.querySelectorAll('.nav-links li a:not(.connexion)');
    const currentPath = window.location.pathname;
  
    // Set initial active link based on URL
    navLinks.forEach(link => {
      if (link.getAttribute('href') === currentPath) {
        link.classList.add('active');
      }
    });
  
    // Handle clicks on all links
    navLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        // Skip if it's the "À propos" link (we'll handle separately)
        if (this.getAttribute('title') === "À propos") return;
        
        // Skip if it's an anchor link
        if (this.getAttribute('href') === '#') return;
        
        // Update active state for regular links
        navLinks.forEach(l => l.classList.remove('active'));
        this.classList.add('active');
      });
    });
  
    // Special handling for "À propos"
    const aproposLink = document.querySelector('a[title="À propos"]');
    if (aproposLink) {
      aproposLink.addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('page-bottom').scrollIntoView({
          behavior: 'smooth'
        });
        
        // Keep the current active link highlighted
        navLinks.forEach(l => {
          l.classList.toggle('active', l.getAttribute('href') === currentPath);
        });
      });
    }
  });
  
  // footer togolloing arrow
  
  document.addEventListener('DOMContentLoaded', function() {
    // Function to handle mobile behavior
    function setupMobileBehavior() {
      const columns = document.querySelectorAll('.footer-column');
      
      columns.forEach(column => {
        const h3 = column.querySelector('h3');
        const links = column.querySelectorAll('a');
        
        // Clear any existing listeners to avoid duplicates
        const newH3 = h3.cloneNode(true);
        h3.parentNode.replaceChild(newH3, h3);
        
        newH3.addEventListener('click', function() {
          this.parentElement.classList.toggle('active');
          if (this.parentElement.classList.contains('active')){
              links.forEach(link => {
            link.style.display = 'block';
          });
          }else{
              links.forEach(link => {
            link.style.display = 'none';
          });
          }
          
        });
        
        // Set initial state for mobile
        if (window.innerWidth <= 576) {
          links.forEach(link => {
            link.style.display = 'none';
          });
        } else {
          links.forEach(link => {
            link.style.display = 'block';
          });
          column.classList.remove('active');
        }
      });
    }
    
    // Initial setup
    setupMobileBehavior();
    
    // Make it responsive when window is resized
    window.addEventListener('resize', function() {
      setupMobileBehavior();
    });
  });
  
  
  // togolling auth 
  document.addEventListener('DOMContentLoaded', function() {
    let currentRole = null;
    
    // Role selection handler
    document.querySelectorAll('.role-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        currentRole = this.dataset.role;
        document.getElementById('roleSelection').style.display = 'none';
        document.getElementById('authForms').style.display = 'block';
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('signupForm').style.display = 'none';
        
        // Hide signup option for admin (special case)
        if (currentRole === 'admin') {
          document.querySelectorAll('.toggle-auth[data-action="signup"]').forEach(el => {
            el.style.display = 'none';
          });
        }
      });
    });
  
    // Toggle between login/signup
    document.querySelectorAll('.toggle-auth').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const action = this.dataset.action;
        document.getElementById('loginForm').style.display = action === 'login' ? 'block' : 'none';
        document.getElementById('signupForm').style.display = action === 'signup' ? 'block' : 'none';
      });
    });
  });
  
  
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize modal
    const modal = new bootstrap.Modal(document.getElementById('vehicleModal'), {
      focus: true,
      keyboard: true
    });
  
    // Cache DOM elements
    const modalTitle = document.getElementById('vehicleModalTitle');
    const modalBody = document.getElementById('vehicleModalBody');
    const bookNowBtn = document.getElementById('bookNowBtn');
  
    // Vehicle features database
    const vehicleDatabase = {
      cars: {
        "Dacia Sandero Stepway": {
          features: ["Climatisation", "GPS", "5 portes", "Automatique"],
          price: "À partir de 120€/jour"
        },
        "Seat Ibiza": {
          features: ["Climatisation automatique", "Ecran tactile", "5 portes", "Manuelle"],
          price: "À partir de 110€/jour"
        },
        "Hyundai i20": {
          features: ["Climatisation", "Caméra de recul", "5 portes", "Manuelle"],
          price: "À partir de 115€/jour"
        },
        "Swift Auto": {
          features: ["Climatisation", "Apple CarPlay", "3 portes", "Automatique"],
          price: "À partir de 130€/jour"
        }
      },
      bikes: {
        "VÉLO BEST BIKE 26": {
          features: ["Cadre aluminium", "Freins V-brake", "21 vitesses", "Pneus polyvalents"],
          price: "À partir de 20€/jour"
        },
        "TRQZNLEP Vélo": {
          features: ["Cadre acier", "Suspension hydraulique", "24 vitesses", "Pneus cramponnés"],
          price: "À partir de 15€/jour"
        },
        "adulte tout-terrain": {
          features: ["Fourche télescopique", "Freins disques", "Transmission 8v", "Porte-bidon"],
          price: "À partir de 17.99€/jour"
        },
        "Vélos de Ville": {
          features: ["Cadre step-through", "Éclairage intégré", "7 vitesses", "Antivol intégré"],
          price: "À partir de 8.99€/jour"
        }
      }
    };
  
    // Handle detail button clicks
    document.querySelectorAll('.btn-details').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const card = this.closest('.vehicle');
        const vehicleTitle = card.getAttribute('title');
        const isBike = card.classList.contains('velo');
        const vehicleType = isBike ? 'bikes' : 'cars';
        
        // Get vehicle data from our database
        const vehicleInfo = vehicleDatabase[vehicleType][vehicleTitle] || {
          features: ["Information non disponible"],
          price: "Prix sur demande"
        };
  
        const vehicleData = {
          title: vehicleTitle,
          image: card.querySelector('.card-img').src,
          description: card.querySelector('p').textContent,
          price: vehicleInfo.price,
          features: vehicleInfo.features
        };
        
        showVehicleDetails(vehicleData);
      });
    });
  
    // Handle vehicle card clicks (excluding buttons)
    document.querySelectorAll('.vehicle').forEach(vehicle => {
      vehicle.addEventListener('click', function(e) {
        if (e.target.closest('.btn-details')) return;
        
        if (this.classList.contains('velo')) {
          // Additional bike-specific behavior
          console.log('Vélo cliqué:', this.getAttribute('title'));
        } else {
          // Additional car-specific behavior
          console.log('Voiture cliquée:', this.getAttribute('title'));
        }
      });
    });
  
    // Booking button handler
    bookNowBtn.addEventListener('click', function() {
      const vehicleName = modalTitle.textContent;
      alert(`Réservation demandée pour : ${vehicleName}`);
      modal.hide();
    });
  
    // Function to display vehicle details
    function showVehicleDetails(data) {
      modalTitle.textContent = data.title;
      
      modalBody.innerHTML = `
        <div class="row">
          <div class="col-md-6">
            <img src="${data.image}" class="img-fluid rounded" alt="${data.title}" loading="lazy">
            <div class="mt-3">
              <h4 style="color:#28a745;">${data.price}</h4>
            </div>
          </div>
          <div class="col-md-6">
            <h4>Description</h4>
            <p>${data.description}</p>
            <h4 class="mt-4">Caractéristiques</h4>
            <ul class="features-list">
              ${data.features.map(feat => `<li>${feat}</li>`).join('')}
            </ul>
          </div>
        </div>
      `;
      
      modal.show();
    }
  });