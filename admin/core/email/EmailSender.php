<?php
class EmailSender {
    private $db;
    
    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }
    
    public function sendDraft($draftId) {
        $draft = $this->getDraftForSending($draftId);
        if (!$draft) {
            throw new Exception('Draft not found or not approved for sending.');
        }
        
        $subject = $draft['subject'];
        $content = $draft['content'];
        $recipientType = $draft['recipient_type'];
        $selectedUsers = json_decode($draft['selected_users'], true) ?? [];
        
        $recipients = $this->getRecipients($recipientType, $selectedUsers);
        $emailsSent = 0;
        $totalEmails = count($recipients);
        
        foreach ($recipients as $recipient) {
            if ($this->sendEmail($recipient['email'], $recipient['name'], $subject, $content)) {
                $emailsSent++;
            }
        }
        
        // Log email activity
        $this->logEmailActivity($draft['admin_id'], $subject, $recipientType, $totalEmails, $emailsSent, $draftId);
        
        return [
            'sent_count' => $emailsSent,
            'total_count' => $totalEmails
        ];
    }
    
    private function getDraftForSending($draftId) {
        $sql = "SELECT * FROM email_drafts WHERE id = ? AND status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $draftId);
        $stmt->execute();
        $result = $stmt->get_result();
        $draft = $result->fetch_assoc();
        $stmt->close();
        return $draft;
    }
    
    private function getRecipients($recipientType, $selectedUsers) {
        if ($recipientType === 'all') {
            $sql = "SELECT email, name FROM users WHERE email IS NOT NULL AND email != ''";
            $result = $this->db->query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        } elseif ($recipientType === 'selected' && !empty($selectedUsers)) {
            $placeholders = str_repeat('?,', count($selectedUsers) - 1) . '?';
            $sql = "SELECT email, name FROM users WHERE user_id IN ($placeholders) AND email IS NOT NULL AND email != ''";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param(str_repeat('i', count($selectedUsers)), ...$selectedUsers);
            $stmt->execute();
            $result = $stmt->get_result();
            $recipients = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $recipients;
        }
        return [];
    }
    
    private function sendEmail($to, $name, $subject, $content) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Sanix Tech <info@sanixtech.in>\r\n";
        $headers .= "Reply-To: info@sanixtech.in\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "List-Unsubscribe: <mailto:unsubscribe@sanixtech.in>\r\n";
        
        $emailTemplate = $this->getEmailTemplate($name, $subject, $content);
        
        return mail($to, $subject, $emailTemplate, $headers);
    }
    
    private function getEmailTemplate($name, $subject, $content) {
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$subject}</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
                .content { padding: 40px 30px; background: #ffffff; }
                .content h2 { color: #333; margin-bottom: 20px; font-size: 24px; }
                .content p { margin-bottom: 16px; }
                .content img { max-width: 100%; height: auto; border-radius: 8px; margin: 16px 0; }
                .footer { background: #f8f9fa; padding: 30px 20px; text-align: center; border-top: 1px solid #e9ecef; }
                .footer p { margin: 5px 0; font-size: 14px; color: #6c757d; }
                .unsubscribe { font-size: 12px; color: #6c757d; margin-top: 20px; }
                .unsubscribe a { color: #6c757d; text-decoration: underline; }
                @media (max-width: 600px) {
                    .content { padding: 20px 15px; }
                    .header { padding: 20px 15px; }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Sanix Tech</h1>
                </div>
                <div class='content'>
                    <p>Hello " . htmlspecialchars($name) . ",</p>
                    {$content}
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Sanix Tech. All rights reserved.</p>
                    <p>This email was sent from our admin panel.</p>
                    <div class='unsubscribe'>
                        <p>Don't want to receive these emails? <a href='mailto:unsubscribe@sanixtech.in?subject=Unsubscribe'>Unsubscribe here</a></p>
                    </div>
                </div>
            </div>
        </body>
        </html>";
    }
    
    private function logEmailActivity($adminId, $subject, $recipientType, $totalEmails, $emailsSent, $draftId) {
        $sql = "INSERT INTO email_logs (admin_id, subject, recipient_type, recipients_count, sent_count, draft_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("issiii", $adminId, $subject, $recipientType, $totalEmails, $emailsSent, $draftId);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
