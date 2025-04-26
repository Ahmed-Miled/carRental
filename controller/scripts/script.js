
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
  
    
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-details').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const vehicleData = JSON.parse(this.dataset.vehicle);

            // Populer le modal
            const modalTitle = document.getElementById('vehicleModalTitle');
            const modalBody = document.getElementById('vehicleModalBody');
            
            modalTitle.textContent = `${vehicleData.marque} ${vehicleData.model}`;
            
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <img src="assets/img/${vehicleData.image}" 
                             class="img-fluid" 
                             alt="${vehicleData.marque} ${vehicleData.model}">
                    </div>
                    <div class="col-md-6">
                        <p><strong>Année:</strong> ${vehicleData.year}</p>
                        <p><strong>Carburant:</strong> ${vehicleData.fuel_type}</p>
                        <p><strong>Kilométrage:</strong> ${vehicleData.mileage} km</p>
                        <p><strong>Description:</strong> ${vehicleData.description}</p>
                        <div class="pricing">
                            <span class="old-price">${vehicleData.price_per_day} €/jour</span>
                            <span class="new-price">${vehicleData.promotional_price} €/jour</span>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('bookNowBtn').href = `views/reservation.php?vehicule_id=${vehicleData.id}`;
            new bootstrap.Modal(document.getElementById('vehicleModal')).show();
        });
    });
});