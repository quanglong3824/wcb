<?php
/**
 * Simple File-based Cache System
 * For caching database queries and API responses
 */

class Cache {
    private static $cacheDir;
    private static $defaultTTL = 300; // 5 minutes
    
    /**
     * Initialize cache directory
     */
    public static function init() {
        self::$cacheDir = dirname(__DIR__) . '/cache';
        
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0755, true);
        }
    }
    
    /**
     * Get cached value
     */
    public static function get($key) {
        self::init();
        
        $file = self::getFilePath($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        
        if (!$data || !isset($data['expires']) || !isset($data['value'])) {
            return null;
        }
        
        // Check if expired
        if ($data['expires'] < time()) {
            self::delete($key);
            return null;
        }
        
        return $data['value'];
    }
    
    /**
     * Set cached value
     */
    public static function set($key, $value, $ttl = null) {
        self::init();
        
        if ($ttl === null) {
            $ttl = self::$defaultTTL;
        }
        
        $file = self::getFilePath($key);
        $data = [
            'key' => $key,
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time()
        ];
        
        return file_put_contents($file, json_encode($data), LOCK_EX) !== false;
    }
    
    /**
     * Delete cached value
     */
    public static function delete($key) {
        self::init();
        
        $file = self::getFilePath($key);
        
        if (file_exists($file)) {
            return unlink($file);
        }
        
        return true;
    }
    
    /**
     * Clear all cache
     */
    public static function clear() {
        self::init();
        
        $files = glob(self::$cacheDir . '/*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    /**
     * Clear expired cache files
     */
    public static function clearExpired() {
        self::init();
        
        $files = glob(self::$cacheDir . '/*.cache');
        $cleared = 0;
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            
            if (!$data || !isset($data['expires']) || $data['expires'] < time()) {
                unlink($file);
                $cleared++;
            }
        }
        
        return $cleared;
    }
    
    /**
     * Remember - get from cache or execute callback
     */
    public static function remember($key, $ttl, $callback) {
        $value = self::get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        self::set($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Get file path for cache key
     */
    private static function getFilePath($key) {
        $hash = md5($key);
        return self::$cacheDir . '/' . $hash . '.cache';
    }
    
    /**
     * Get cache statistics
     */
    public static function getStats() {
        self::init();
        
        $files = glob(self::$cacheDir . '/*.cache');
        $totalSize = 0;
        $validCount = 0;
        $expiredCount = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            
            if ($data && isset($data['expires'])) {
                if ($data['expires'] >= time()) {
                    $validCount++;
                } else {
                    $expiredCount++;
                }
            }
        }
        
        return [
            'total_files' => count($files),
            'valid_count' => $validCount,
            'expired_count' => $expiredCount,
            'total_size' => $totalSize,
            'total_size_formatted' => formatBytes($totalSize)
        ];
    }
}

/**
 * Format bytes helper
 */
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}

/**
 * Helper functions for quick access
 */
function cache_get($key) {
    return Cache::get($key);
}

function cache_set($key, $value, $ttl = 300) {
    return Cache::set($key, $value, $ttl);
}

function cache_delete($key) {
    return Cache::delete($key);
}

function cache_remember($key, $ttl, $callback) {
    return Cache::remember($key, $ttl, $callback);
}

function cache_clear() {
    return Cache::clear();
}
