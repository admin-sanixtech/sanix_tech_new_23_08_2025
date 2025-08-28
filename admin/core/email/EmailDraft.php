<?php
// classes/EmailDraft.php
class EmailDraft {
    private $db;
    
    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }
    
    public function createDraft($adminId, $subject, $content, $recipientType, $selectedUsers, $status, $createdBy) {
        $selectedUsersJson = json_encode($selectedUsers);
        
        $sql = "INSERT INTO email_drafts (admin_id, subject, content, recipient_type, selected_users, status, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssssi", $adminId, $subject, $content, $recipientType, $selectedUsersJson, $status, $createdBy);
        
        if ($stmt->execute()) {
            $draftId = $this->db->insert_id;
            $stmt->close();
            return $draftId;
        }
        
        $stmt->close();
        return false;
    }
    
    public function getDraftsByAdmin($adminId, $limit = 20) {
        $sql = "SELECT ed.*, u.name as creator_name, a.name as approver_name 
                FROM email_drafts ed 
                LEFT JOIN users u ON ed.created_by = u.user_id 
                LEFT JOIN users a ON ed.approved_by = a.user_id 
                WHERE ed.admin_id = ? 
                ORDER BY ed.created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $adminId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $drafts = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $drafts;
    }
    
    public function getPendingApprovals() {
        $sql = "SELECT ed.*, u.name as creator_name 
                FROM email_drafts ed 
                JOIN users u ON ed.created_by = u.user_id 
                WHERE ed.status = 'pending_approval' 
                ORDER BY ed.created_at ASC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    public function getDraftById($draftId) {
        $sql = "SELECT * FROM email_drafts WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $draftId);
        $stmt->execute();
        $result = $stmt->get_result();
        $draft = $result->fetch_assoc();
        $stmt->close();
        return $draft;
    }
    
    public function updateDraftStatus($draftId, $status, $approvedBy = null, $notes = null) {
        if ($approvedBy) {
            $sql = "UPDATE email_drafts SET status = ?, approved_by = ?, approval_notes = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sisi", $status, $approvedBy, $notes, $draftId);
        } else {
            $sql = "UPDATE email_drafts SET status = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $status, $draftId);
        }
        
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    public function markAsSent($draftId) {
        $sql = "UPDATE email_drafts SET status = 'sent', sent_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $draftId);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>
