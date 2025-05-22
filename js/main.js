// JavaScript principal
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du panier avec AJAX
    setupCartAjax();
    
    // Gestion des avis avec AJAX
    setupReviewsAjax();
});

// Configuration des fonctionnalités AJAX pour le panier
function setupCartAjax() {
    // Ajouter au panier
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const quantity = document.querySelector('#quantity') ? document.querySelector('#quantity').value : 1;
            
            // Requête AJAX pour ajouter au panier
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour le compteur du panier
                    const cartCounter = document.querySelector('#cart-counter');
                    if (cartCounter) {
                        cartCounter.textContent = data.totalItems;
                        cartCounter.style.display = data.totalItems > 0 ? 'flex' : 'none';
                    }
                    
                    // Afficher un message de succès
                    showNotification('Produit ajouté au panier', 'success');
                } else {
                    showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        });
    });
    
    // Mettre à jour la quantité dans le panier
    const quantityInputs = document.querySelectorAll('.cart-quantity-input');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartItemId = this.dataset.cartItemId;
            const newQuantity = this.value;
            
            if (newQuantity < 1) {
                this.value = 1;
                return;
            }
            
            updateCartItem(cartItemId, newQuantity);
        });
    });
    
    // Supprimer du panier
    const removeButtons = document.querySelectorAll('.remove-from-cart-btn');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const cartItemId = this.dataset.cartItemId;
            
            // Requête AJAX pour supprimer du panier
            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `cart_item_id=${cartItemId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer l'élément du DOM
                    const cartItem = document.querySelector(`.cart-item[data-cart-item-id="${cartItemId}"]`);
                    if (cartItem) {
                        cartItem.remove();
                    }
                    
                    // Mettre à jour le total
                    updateCartTotal(data.totalPrice);
                    
                    // Mettre à jour le compteur du panier
                    const cartCounter = document.querySelector('#cart-counter');
                    if (cartCounter) {
                        cartCounter.textContent = data.totalItems;
                        cartCounter.style.display = data.totalItems > 0 ? 'flex' : 'none';
                    }
                    
                    // Afficher un message de succès
                    showNotification('Produit retiré du panier', 'success');
                    
                    // Si le panier est vide, afficher un message
                    if (data.totalItems === 0) {
                        const cartContainer = document.querySelector('.cart-items');
                        if (cartContainer) {
                            cartContainer.innerHTML = '<p>Votre panier est vide.</p>';
                        }
                    }
                } else {
                    showNotification(data.message || 'Erreur lors de la suppression', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        });
    });
}

// Mettre à jour un élément du panier
function updateCartItem(cartItemId, quantity) {
    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `cart_item_id=${cartItemId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour le sous-total de l'élément
            const subtotalElement = document.querySelector(`.cart-item[data-cart-item-id="${cartItemId}"] .item-subtotal`);
            if (subtotalElement) {
                subtotalElement.textContent = `${data.itemSubtotal.toFixed(2)} €`;
            }
            
            // Mettre à jour le total du panier
            updateCartTotal(data.totalPrice);
            
            // Mettre à jour le compteur du panier
            const cartCounter = document.querySelector('#cart-counter');
            if (cartCounter) {
                cartCounter.textContent = data.totalItems;
            }
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Une erreur est survenue', 'error');
    });
}

// Mettre à jour le total du panier
function updateCartTotal(totalPrice) {
    const totalElement = document.querySelector('.cart-total-price');
    if (totalElement) {
        totalElement.textContent = `${totalPrice.toFixed(2)} €`;
    }
}

// Configuration des fonctionnalités AJAX pour les avis
function setupReviewsAjax() {
    const reviewForm = document.querySelector('#review-form');
    
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.productId;
            const rating = document.querySelector('input[name="rating"]:checked').value;
            const comment = document.querySelector('#review-comment').value;
            
            // Requête AJAX pour soumettre un avis
            fetch('/reviews/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `product_id=${productId}&rating=${rating}&comment=${encodeURIComponent(comment)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ajouter le nouvel avis à la liste
                    const reviewsList = document.querySelector('.reviews-list');
                    if (reviewsList) {
                        const newReview = document.createElement('div');
                        newReview.className = 'review-item';
                        newReview.innerHTML = `
                            <div class="review-header">
                                <div class="review-author">${data.review.author}</div>
                                <div class="review-rating">${'★'.repeat(data.review.rating)}${'☆'.repeat(5 - data.review.rating)}</div>
                                <div class="review-date">${data.review.date}</div>
                            </div>
                            <div class="review-content">${data.review.comment}</div>
                        `;
                        reviewsList.prepend(newReview);
                    }
                    
                    // Réinitialiser le formulaire
                    reviewForm.reset();
                    
                    // Afficher un message de succès
                    showNotification('Votre avis a été publié', 'success');
                } else {
                    showNotification(data.message || 'Erreur lors de la soumission de l\'avis', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        });
    }
    
    // Supprimer un avis
    const deleteReviewButtons = document.querySelectorAll('.delete-review-btn');
    
    deleteReviewButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')) {
                return;
            }
            
            const reviewId = this.dataset.reviewId;
            
            // Requête AJAX pour supprimer un avis
            fetch('/reviews/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `review_id=${reviewId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer l'élément du DOM
                    const reviewItem = document.querySelector(`.review-item[data-review-id="${reviewId}"]`);
                    if (reviewItem) {
                        reviewItem.remove();
                    }
                    
                    // Afficher un message de succès
                    showNotification('Avis supprimé avec succès', 'success');
                } else {
                    showNotification(data.message || 'Erreur lors de la suppression de l\'avis', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showNotification('Une erreur est survenue', 'error');
            });
        });
    });
}

// Afficher une notification
function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Afficher la notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Masquer et supprimer la notification après 3 secondes
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
