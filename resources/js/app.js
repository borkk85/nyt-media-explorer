import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Initialize favorite buttons
document.addEventListener('DOMContentLoaded', function() {
    // Handle favorite buttons
    const favoriteButtons = document.querySelectorAll('.favorite-button');
    if (favoriteButtons.length > 0) {
        favoriteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const id = this.dataset.id;
                const type = this.dataset.type;
                
                // Make ajax request to toggle favorite status
                fetch(`/favorites/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: id,
                        type: type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Update the button appearance based on the new state
                    const svg = this.querySelector('svg');
                    if (data.favorited) {
                        svg.setAttribute('fill', 'currentColor');
                        svg.setAttribute('color', 'gold');
                    } else {
                        svg.setAttribute('fill', 'none');
                        svg.setAttribute('color', 'currentColor');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    }

    // Handle rating stars in review forms
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    if (ratingInputs.length > 0) {
        ratingInputs.forEach(input => {
            input.addEventListener('change', function() {
                const value = this.value;
                const stars = document.querySelectorAll('label[for^="rating-"] svg');
                
                stars.forEach((star, index) => {
                    if (index < value) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            });
        });

        // Add click handler to the star labels for better UX
        const starLabels = document.querySelectorAll('label[for^="rating-"]');
        starLabels.forEach(label => {
            label.addEventListener('click', function() {
                const input = document.getElementById(this.getAttribute('for'));
                if (input) {
                    input.checked = true;
                    input.dispatchEvent(new Event('change'));
                }
            });
        });
    }
});