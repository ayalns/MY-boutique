<?php
// Contrôleur pour les événements
class EventController extends Controller {
    private $eventModel;
    
    public function __construct() {
        $this->eventModel = new EventModel();
    }
    
    // Afficher la liste des événements
    public function index() {
        $events = $this->eventModel->getAllEvents();
        $this->render('event/index', ['events' => $events]);
    }
    
    // Afficher un événement spécifique
    public function show($id) {
        $event = $this->eventModel->getEvent($id);
        
        if (!$event) {
            $this->redirect('/events');
        }
        
        $this->render('event/show', ['event' => $event]);
    }
    
    // Afficher les événements de l'utilisateur connecté
    public function userEvents() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $events = $this->eventModel->getUserEvents($userId);
        
        $this->render('event/user', ['events' => $events]);
    }
    
    // Afficher le formulaire d'ajout d'événement
    public function showAddEvent() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $this->render('event/form', ['event' => null]);
    }
    
    // Traiter l'ajout d'un événement
    public function addEvent() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events/add');
        }
        
        $userId = $_SESSION['user_id'];
        
        $eventData = [
            'user_id' => $userId,
            'titre' => sanitize($_POST['titre']),
            'description' => sanitize($_POST['description']),
            'lieu' => sanitize($_POST['lieu']),
            'date_evenement' => sanitize($_POST['date_evenement']),
            'heure_evenement' => sanitize($_POST['heure_evenement']),
            'image' => isset($_POST['image']) ? sanitize($_POST['image']) : 'event1.png',
            'date_creation' => date('Y-m-d H:i:s')
        ];
        
        // Créer l'événement et récupérer l'ID
        $newEventId = $this->eventModel->createEvent($eventData);
        
        // Vérifier si l'événement a été créé avec succès
        if ($newEventId > 0) {
            $_SESSION['success'] = "L'événement a été créé avec succès.";
            $this->redirect('/events/user');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la création de l'événement.";
            $this->redirect('/events/add');
        }
    }
    
    // Afficher le formulaire de modification d'un événement
    public function showEditEvent($id) {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        $userId = $_SESSION['user_id'];
        $event = $this->eventModel->getEvent($id);
        
        // Vérifier que l'événement existe et appartient à l'utilisateur
        if (!$event || ($event['user_id'] != $userId && !isAdmin())) {
            $this->redirect('/events/user');
        }
        
        $this->render('event/form', ['event' => $event]);
    }
    
    // Traiter la modification d'un événement
    public function updateEvent() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events/user');
        }
        
        $userId = $_SESSION['user_id'];
        $eventId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        
        $eventData = [
            'titre' => sanitize($_POST['titre']),
            'description' => sanitize($_POST['description']),
            'lieu' => sanitize($_POST['lieu']),
            'date_evenement' => sanitize($_POST['date_evenement']),
            'heure_evenement' => sanitize($_POST['heure_evenement']),
            'image' => isset($_POST['image']) ? sanitize($_POST['image']) : 'event1.png',
            'date_modification' => date('Y-m-d H:i:s')
        ];
        
        // Mettre à jour l'événement
        $success = $this->eventModel->updateEvent($eventId, $userId, $eventData);
        
        if ($success) {
            $_SESSION['success'] = "L'événement a été mis à jour avec succès.";
            $this->redirect('/events/user');
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de l'événement.";
            $this->redirect('/events/edit/' . $eventId);
        }
    }
    
    // Traiter la suppression d'un événement
    public function deleteEvent() {
        // Vérifier que l'utilisateur est connecté
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events/user');
        }
        
        $userId = $_SESSION['user_id'];
        $eventId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        
        // Supprimer l'événement
        $success = $this->eventModel->deleteEvent($eventId, $userId);
        
        if ($success) {
            $_SESSION['success'] = "L'événement a été supprimé avec succès.";
        } else {
            $_SESSION['error'] = "Une erreur est survenue lors de la suppression de l'événement.";
        }
        
        $this->redirect('/events/user');
    }
}

