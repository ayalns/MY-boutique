<?php
// Contrôleur pour les avis et commentaires
class ReviewController extends Controller {
    private $reviewModel;
    private $productModel;
    
    public function __construct() {
        $this->reviewModel = new ReviewModel();
        $this->productModel = new ProductModel();
    }
    
    // Soumettre un avis (AJAX)
    public function submitReview() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        // Vérifier si la requête est AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Méthode non autorisée']);
            } else {
                $this->redirect('/');
            }
        }
        
        $userId = $_SESSION['user_id'];
        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $commentaire = trim($_POST['commentaire']);
        $note = isset($_POST['note']) && is_numeric($_POST['note']) ? (int) $_POST['note'] : null;
        
        // Validation
        if (!$productId || $commentaire === '' || $note < 1 || $note > 5) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Données invalides']);
            } else {
                $this->redirect('/product/' . $productId);
            }
        }
        
        // Vérifier si l'utilisateur a déjà donné son avis
        if ($this->reviewModel->hasUserReviewed($userId, $productId)) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Vous avez déjà donné votre avis sur ce produit']);
            } else {
                $_SESSION['error'] = "Vous avez déjà donné votre avis sur ce produit.";
                $this->redirect('/product/' . $productId);
            }
        }
        
        // Ajouter l'avis
        $reviewId = $this->reviewModel->addReview($userId, $productId, $commentaire, $note);
        
        if ($reviewId) {
            // Récupérer les informations de l'utilisateur
            $user = (new UserModel())->find($userId);
            
            if ($isAjax) {
                $this->json([
                    'success' => true,
                    'message' => 'Avis ajouté avec succès',
                    'userName' => $user['prenom'] . ' ' . $user['nom'],
                    'commentaire' => $commentaire,
                    'note' => $note,
                    'date' => date('d/m/Y')
                ]);
            } else {
                $_SESSION['success'] = "Votre avis a été ajouté avec succès.";
                $this->redirect('/product/' . $productId);
            }
        } else {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Erreur lors de l\'ajout de l\'avis']);
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de l'ajout de votre avis.";
                $this->redirect('/product/' . $productId);
            }
        }
    }
    
    // Afficher le formulaire de modification d'un avis
    public function showEditReview($reviewId) {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer l'avis
        $review = $this->reviewModel->getReview($reviewId);
        
        // Vérifier que l'avis existe et appartient à l'utilisateur
        if (!$review || $review['user_id'] != $userId) {
            $this->redirect('/profile');
        }
        
        $this->render('review/edit', ['review' => $review]);
    }
    
    // Modifier un avis (AJAX)
    public function editReview() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        // Vérifier si la requête est AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Méthode non autorisée']);
            } else {
                $this->redirect('/profile');
            }
        }
        
        $userId = $_SESSION['user_id'];
        $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        $commentaire = trim($_POST['commentaire']);
        $note = isset($_POST['note']) && is_numeric($_POST['note']) ? (int) $_POST['note'] : null;
        
        // Validation
        if (!$reviewId || $commentaire === '' || $note < 1 || $note > 5) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Données invalides']);
            } else {
                $this->redirect('/review/edit/' . $reviewId);
            }
        }
        
        // Mettre à jour l'avis
        $success = $this->reviewModel->updateReview($reviewId, $userId, $commentaire, $note);
        
        if ($success) {
            // Récupérer l'avis mis à jour
            $review = $this->reviewModel->getReview($reviewId);
            
            if ($isAjax) {
                $this->json([
                    'success' => true,
                    'message' => 'Avis mis à jour avec succès',
                    'review' => $review
                ]);
            } else {
                $_SESSION['success'] = "Votre avis a été mis à jour avec succès.";
                $this->redirect('/product/' . $review['product_id']);
            }
        } else {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Erreur lors de la mise à jour de l\'avis']);
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de votre avis.";
                $this->redirect('/review/edit/' . $reviewId);
            }
        }
    }
    
    // Supprimer un avis (AJAX)
    public function deleteReview() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        // Vérifier si la requête est AJAX
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Méthode non autorisée']);
            } else {
                $this->redirect('/profile');
            }
        }
        
        $userId = $_SESSION['user_id'];
        $reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
        
        // Validation
        if (!$reviewId) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'ID d\'avis invalide']);
            } else {
                $this->redirect('/profile');
            }
        }
        
        // Récupérer l'avis pour connaître le produit associé
        $review = $this->reviewModel->getReview($reviewId);
        
        if (!$review) {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Avis introuvable']);
            } else {
                $this->redirect('/profile');
            }
        }
        
        $productId = $review['product_id'];
        
        // Supprimer l'avis
        $success = $this->reviewModel->deleteUserReview($reviewId, $userId);
        
        if ($success) {
            if ($isAjax) {
                $this->json([
                    'success' => true,
                    'message' => 'Avis supprimé avec succès'
                ]);
            } else {
                $_SESSION['success'] = "Votre avis a été supprimé avec succès.";
                $this->redirect('/product/' . $productId);
            }
        } else {
            if ($isAjax) {
                $this->json(['success' => false, 'message' => 'Erreur lors de la suppression de l\'avis']);
            } else {
                $_SESSION['error'] = "Une erreur est survenue lors de la suppression de votre avis.";
                $this->redirect('/product/' . $productId);
            }
        }
    }
}