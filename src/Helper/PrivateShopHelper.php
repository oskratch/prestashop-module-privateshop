<?php
    class PrivateShopHelper {

        public static function copyEmailTemplates() {
            $modulePath = _PS_MODULE_DIR_ . 'privateshop/mails/';
            $themePath = _PS_THEME_DIR_ . 'modules/privateshop/mails/';
    
            if (!file_exists($themePath)) {
                mkdir($themePath, 0755, true);
            }
    
            $languages = glob($modulePath . '*', GLOB_ONLYDIR);
    
            foreach ($languages as $languageDir) {
                $languageCode = basename($languageDir);
                $destLanguageDir = $themePath . $languageCode;
    
                if (!file_exists($destLanguageDir)) {
                    mkdir($destLanguageDir, 0755, true);
                }
    
                self::copyDirectory($languageDir, $destLanguageDir);
            }
        }
    
        private static function copyDirectory($source, $destination) {
            $files = scandir($source);
    
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $srcPath = $source . '/' . $file;
                    $destPath = $destination . '/' . $file;
    
                    if (is_dir($srcPath)) {
                        if (!file_exists($destPath)) {
                            mkdir($destPath, 0755, true);
                        }
                        self::copyDirectory($srcPath, $destPath);
                    } else {
                        copy($srcPath, $destPath);
                    }
                }
            }
        }

        public function deleteFolder($folderPath) {
            if (!is_dir($folderPath)) {
                return false;
            }
            foreach (scandir($folderPath) as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $filePath = $folderPath . DIRECTORY_SEPARATOR . $item;
                if (is_dir($filePath)) {
                    $this->deleteFolder($filePath);
                } else {
                    unlink($filePath);
                }
            }
            return rmdir($folderPath);
        }            
               
    }
