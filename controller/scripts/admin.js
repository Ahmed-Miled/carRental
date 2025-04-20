// admin.js
console.log('Admin JS loaded');

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM ready');
    
    // Navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // thabet kan log out essay
            if (this.closest('.logout-item')) {
                // If it's the logout link, DO NOTHING here, let the default action proceed
                return;
            }
            e.preventDefault();
            console.log('Clicked:', this.dataset.target);
            
            // Remove active classes
            document.querySelectorAll('.nav-item, .content-section').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active classes
            this.classList.add('active');
            const target = this.dataset.target;
            if(target) document.getElementById(target).classList.add('active');
        });
    });

    // Agency Form Toggle
    const addBtn = document.querySelector('.btn-add');
    const agencyForm = document.querySelector('.add-agency-form');
    if(addBtn && agencyForm) {
        addBtn.addEventListener('click', () => {
            agencyForm.style.display = 'block';
        });
        document.querySelector('.btn-cancel').addEventListener('click', () => {
            agencyForm.style.display = 'none';
        });
    }
    /*
    // Delete confirmation dialogs
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    */
});

/*
// admin.js
document.addEventListener('DOMContentLoaded', () => {
    console.log('Admin JS loaded - DOM ready');

    // UI Elements
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="spinner"></div>
        <p>Processing...</p>
    `;

    // Preserve active tab on page reload
    const activeTab = localStorage.getItem('activeTab') || 'users';
    document.querySelectorAll(`[data-target="${activeTab}"]`).forEach(el => {
        el.classList.add('active');
        document.getElementById(activeTab).classList.add('active');
    });

    // Navigation System
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.dataset.target;
            if (!target) return;

            // Update UI state
            document.querySelectorAll('.nav-item, .content-section').forEach(el => {
                el.classList.remove('active');
            });
            this.classList.add('active');
            document.getElementById(target).classList.add('active');

            // Persist state
            localStorage.setItem('activeTab', target);
            window.location.hash = target;
            
            console.log('Navigated to:', target);
        });
    });

    // Agency Form Toggle
    const addBtn = document.querySelector('.btn-add');
    const agencyForm = document.querySelector('.add-agency-form');
    if(addBtn && agencyForm) {
        addBtn.addEventListener('click', () => {
            agencyForm.style.display = 'block';
            addBtn.style.display = 'none';
        });
        
        document.querySelector('.btn-cancel').addEventListener('click', () => {
            agencyForm.style.display = 'none';
            addBtn.style.display = 'block';
        });
    }

    // Delete Confirmation System
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
                console.log('Delete action canceled');
            }
        });
    });

   

    // Image Error Handling
    document.querySelectorAll('.car-card img').forEach(img => {
        img.addEventListener('error', function() {
            this.style.opacity = '0.5';
            console.warn('Image failed to load:', this.src);
        });
    });

    // Search Functionality (placeholder)
    document.querySelectorAll('.search-input').forEach(search => {
        search.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            console.log('Searching for:', term);
            // Implement actual search logic here
        });
    });

    // Cleanup when leaving page
    window.addEventListener('beforeunload', () => {
        if (document.body.contains(loadingOverlay)) {
            document.body.removeChild(loadingOverlay);
        }
    });
});
*/