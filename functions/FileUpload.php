<?php
/**
 * File Upload Handler
 */

class FileUpload {
    private $upload_dir;
    private $max_size;
    private $allowed_extensions;

    public function __construct() {
        $this->upload_dir = UPLOAD_DIR;
        $this->max_size = MAX_UPLOAD_SIZE;
        $this->allowed_extensions = explode(',', ALLOWED_EXTENSIONS);

        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }

    public function upload($file, $folder = 'general') {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'No file uploaded'];
        }

        if ($file['size'] > $this->max_size) {
            return ['success' => false, 'message' => 'File size exceeds limit'];
        }

        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $this->allowed_extensions)) {
            return ['success' => false, 'message' => 'File type not allowed'];
        }

        $new_name = uniqid() . '.' . $file_ext;
        $upload_path = $this->upload_dir . $folder . '/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $full_path = $upload_path . $new_name;

        if (move_uploaded_file($file['tmp_name'], $full_path)) {
            // Optimize image if it's an image file
            if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $this->optimizeImage($full_path);
            }

            return [
                'success' => true,
                'filename' => $new_name,
                'path' => $folder . '/' . $new_name,
                'message' => 'File uploaded successfully'
            ];
        }

        return ['success' => false, 'message' => 'Failed to upload file'];
    }

    private function optimizeImage($file_path) {
        if (!extension_loaded('gd')) {
            return false;
        }

        $image_type = exif_imagetype($file_path);
        
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file_path);
                imagejpeg($image, $file_path, 75);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file_path);
                imagepng($image, $file_path, 6);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file_path);
                imagegif($image, $file_path);
                break;
        }

        if (isset($image)) {
            imagedestroy($image);
            return true;
        }

        return false;
    }

    public function deleteFile($file_path) {
        $full_path = $this->upload_dir . $file_path;

        if (file_exists($full_path)) {
            return unlink($full_path);
        }

        return false;
    }
}
?>