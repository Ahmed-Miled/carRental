document.addEventListener('DOMContentLoaded', function() {
    // Initialize the modal
    const vehicleModal = new bootstrap.Modal(document.getElementById('vehicleModal'));
    
    // Vehicle data - move this to a separate file if it grows large
    const vehicleDatabase = {
      // ... your vehicle data here ...
    };
  
    // Handle all detail button clicks
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('btn-details')) {
        e.preventDefault();
        const vehicleCard = e.target.closest('.vehicle');
        const vehicleTitle = vehicleCard.getAttribute('title');
        const isBike = vehicleCard.classList.contains('velo');
        
        // Get vehicle data
        const vehicleData = getVehicleData(vehicleTitle, isBike);
        
        // Display in modal
        showVehicleModal(vehicleData);
      }
    });
  
    function getVehicleData(title, isBike) {
      const type = isBike ? 'bikes' : 'cars';
      const vehicleInfo = vehicleDatabase[type][title] || {
        features: ["Information non disponible"],
        price: "Prix sur demande"
      };
      
      const card = document.querySelector(`.vehicle[title="${title}"]`);
      
      return {
        title: title,
        image: card.querySelector('.card-img').src,
        description: card.querySelector('p').textContent,
        price: vehicleInfo.price,
        features: vehicleInfo.features
      };
    }
  
    function showVehicleModal(data) {
      const modalTitle = document.getElementById('vehicleModalTitle');
      const modalBody = document.getElementById('vehicleModalBody');
      
      // Update modal content
      modalTitle.textContent = data.title;
      modalBody.innerHTML = `
        <div class="row">
          <div class="col-md-6">
            <img src="${data.image}" class="img-fluid rounded mb-3" alt="${data.title}">
            <div class="price-badge">${data.price}</div>
          </div>
          <div class="col-md-6">
            <h5>Description</h5>
            <p>${data.description}</p>
            <h5 class="mt-4">Caractéristiques</h5>
            <ul class="features-list">
              ${data.features.map(feat => `<li>${feat}</li>`).join('')}
            </ul>
          </div>
        </div>
      `;
      
      // Set up booking button
      document.getElementById('bookNowBtn').onclick = function() {
        alert(`Réservation demandée pour : ${data.title}`);
        vehicleModal.hide();
      };
      
      // Show the modal
      vehicleModal.show();
    }
  });