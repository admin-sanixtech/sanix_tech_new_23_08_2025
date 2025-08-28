<?php
class ImageUploader {
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxSize = 2097152; // 2MB
    private $uploadPath;
    private $db;
    
    public function __construct($uploadPath, Database $database) {
        $this->uploadPath = $uploadPath;
        $this->db = $database->getConnection();
        
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    public function upload($file, $adminId) {
        $fileType = $file['type'];
        $fileName = $file['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Validate file type and extension
        if (!in_array($fileType, $this->allowedTypes) || !in_array($fileExtension, $this->allowedExtensions)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        }
        
        // File size validation
        if ($file['size'] > $this->maxSize) {
            throw new Exception('File size too large. Maximum 2MB allowed for email images.');
        }
        
        // Check if file is actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('File is not a valid image.');
        }
        
        // Generate secure filename
        $safeFileName = uniqid('email_img_') . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $fileName);
        $imagePath = $this->uploadPath . $safeFileName;
        
        if (move_uploaded_file($file['tmp_name'], $imagePath)) {
            // Log image upload
            $this->logUpload($adminId, $safeFileName, $fileName, $file['size']);
            
            return [
                'success' => true,
                'url' => BASE_URL . '/uploads/email_images/' . $safeFileName,
                'filename' => $safeFileName
            ];
        } else {
            throw new Exception('Failed to save uploaded file.');
        }
    }
    
    private function logUpload($adminId, $filename, $originalName, $fileSize) {
        $sql = "INSERT INTO image_uploads (admin_id, filename, original_name, file_size, upload_time) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("issi", $adminId, $filename, $originalName, $fileSize);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>