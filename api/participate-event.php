<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/EventParticipantController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/EmailController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$eventId = $_POST['eventId'] ?? null;
$email = $_POST['email'] ?? null;
$userId = $_SESSION['user_id'] ?? null;

if (!$eventId) {
    echo json_encode(['success' => false, 'message' => 'Event ID is required.']);
    exit;
}

// If not logged in, email is required
if (!$userId && empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required for guests.']);
    exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// If logged in, fetch user details to get email if needed
$userFirstName = 'Participant';
if ($userId) {
    $userController = new UserController();
    $user = $userController->getById($userId);
    if ($user) {
        $email = $user->getEmail();
        $userFirstName = $user->getFirstName();
    }
}

// Fetch event details for the email
$eventController = new EventController();
$event = $eventController->getById($eventId);
if (!$event) {
    echo json_encode(['success' => false, 'message' => 'Event not found.']);
    exit;
}

$participantController = new EventParticipantController();
$participant = new EventParticipant(null, $eventId, $userId, $email);

$result = $participantController->save($participant);

if ($result['success']) {
    // Send Email
    $emailController = new EmailController();
    $templateData = [
        'name' => $userFirstName,
        'eventTitle' => $event['title'],
        'eventDate' => $event['createdAt'], // or specific event date if it exists
        'eventUrl' => 'http://localhost/jamis-art/views/landing/event-details.php?id=' . $eventId
    ];
    
    $emailSent = $emailController->sendTemplateEmail(
        $email,
        "Registration Confirmation: " . $event['title'],
        'event-participation.php',
        $templateData
    );

    if (!$emailSent) {
        // We still consider it a success, but warn about the email
        $result['message'] = 'Registered successfully, but failed to send confirmation email.';
    } else {
        $result['message'] = 'Successfully registered! A confirmation email has been sent.';
    }
}

echo json_encode($result);
