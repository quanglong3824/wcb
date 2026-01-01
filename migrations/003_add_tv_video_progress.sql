-- Add TV Video Progress Table
-- Migration: 003_add_tv_video_progress.sql
-- Date: 2026-01-01
-- Purpose: Track video playback progress for management dashboard

CREATE TABLE IF NOT EXISTS tv_video_progress (
    tv_id INT PRIMARY KEY,
    content_id INT DEFAULT NULL,
    current_time DECIMAL(10,2) DEFAULT 0.00,
    duration DECIMAL(10,2) DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tv_id) REFERENCES tvs(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES media(id) ON DELETE SET NULL,
    
    INDEX idx_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
