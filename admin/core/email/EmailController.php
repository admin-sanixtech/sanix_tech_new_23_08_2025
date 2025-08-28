<?php
require_once 'config/config.php';
require_once 'Database.php';
require_once 'EmailDraft.php';
require_once 'ContentFilter.php';
require_once 'ImageUploader.php';
require_once 'EmailSender.php';
require_once 'middleware/AuthMiddleware.php';

class EmailController {
    private $db;
    private $emailDraft;
    private $contentFilter;
    private $imageUploader;
    private $emailSender;
    
    public function __construct() {
        // Check authentication
        AuthMiddleware::requireAdmin();
        
        $this->db = new Database();
        $this->emailDraft = new EmailDraft($this->db);
        $this->contentFilter = new ContentFilter();
        $this->imageUploader = new ImageUploader(UPLOAD_PATH, $this->db);
        $this->emailSender = new EmailSender($this->db);
    }
    
    public function handleRequest() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_FILES['imageUpload'])) {
                    $this->handleImageUpload();
                } elseif (isset($_POST['action']) && $_POST['action'] === 'save_draft') {
                    $this->handleSaveDraft();
                } elseif (isset($_POST['approval_action'])) {
                    $this->handleApproval();
                } elseif (isset($_POST['send_action'])) {
                    $this->handleSendEmail();
                }
            } else {
                $this->showEmailForm();
            }
        } catch (Exception $e) {
            $this->sendJsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    private function handleImageUpload() {
        header('Content-Type: application/json');
        try {
            $result = $this->imageUploader->upload($_FILES['imageUpload'], $_SESSION['user_id']);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    private function handleSaveDraft() {
        header('Content-Type: application/json');
        
        $subject = trim($_POST['subject']);
        $content = $_POST['content'];
        $recipientType = $_POST['recipient_type'];
        $selectedUsers = isset($_POST['selected_users']) ? $_POST['selected_users'] : [];
        
        if (empty($subject) || empty($content)) {
            throw new Exception('Subject and content are required.');
        }
        
        $contentCheck = $this->contentFilter->checkContent($content, $subject);
        $status = $contentCheck['needs_approval'] ? 'pending_approval' : 'draft';
        
        $draftId = $this->emailDraft->createDraft(
            $_SESSION['user_id'],
            $subject,
            $content,
            $recipientType,
            $selectedUsers,
            $status,
            $_SESSION['user_id']
        );
        
        if ($draftId) {
            $this->sendJsonResponse([
                'success' => true,
                'message' => $contentCheck['needs_approval'] ? 
                    'Draft saved and submitted for approval due to flagged content.' : 
                    'Draft saved successfully.',
                'draft_id' => $draftId,
                'status' => $status,
                'flagged_content' => $contentCheck['flagged_content']
            ]);
        } else {
            throw new Exception('Failed to save draft.');
        }
    }
    
    private function handleApproval() {
        header('Content-Type: application/json');
        
        $draftId = intval($_POST['draft_id']);
        $action = $_POST['approval_action'];
        $notes = trim($_POST['approval_notes'] ?? '');
        
        $newStatus = ($action === 'approve') ? 'approved' : 'rejected';
        
        if ($this->emailDraft->updateDraftStatus($draftId, $newStatus, $_SESSION['user_id'], $notes)) {
            $this->sendJsonResponse([
                'success' => true,
                'message' => "Draft {$action}d successfully."
            ]);
        } else {
            throw new Exception("Failed to {$action} draft.");
        }
    }
    
    private function handleSendEmail() {
        header('Content-Type: application/json');
        
        $draftId = intval($_POST['draft_id']);
        $result = $this->emailSender->sendDraft($draftId);
        
        // Mark draft as sent
        $this->emailDraft->markAsSent($draftId);
        
        $this->sendJsonResponse([
            'success' => true,
            'message' => "Emails sent successfully! ({$result['sent_count']}/{$result['total_count']} delivered)",
            'sent_count' => $result['sent_count'],
            'total_count' => $result['total_count']
        ]);
    }
    
    public function showEmailForm() {
        // Get data for the view
        $users = $this->getUsers();
        $drafts = $this->emailDraft->getDraftsByAdmin($_SESSION['user_id']);
        $pendingApprovals = $this->emailDraft->getPendingApprovals();
        
        // Include the view
        include 'views/email_form.php';
    }
    
    private function getUsers() {
        $sql = "SELECT user_id, name, email, role FROM users WHERE email IS NOT NULL AND email != '' ORDER BY name ASC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>