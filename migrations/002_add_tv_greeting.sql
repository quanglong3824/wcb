-- Add TV Greeting (Phòng chào đoàn)
-- Migration: 002_add_tv_greeting.sql
-- Date: 2026-01-01

-- Insert new TV for Greeting Room
INSERT INTO tvs (id, name, location, folder, status, created_at) 
VALUES (8, 'TV Greeting', 'Phòng chào đoàn - Aurora Hotel Plaza', 'greeting', 'online', NOW())
ON DUPLICATE KEY UPDATE
    name = 'TV Greeting',
    location = 'Phòng chào đoàn - Aurora Hotel Plaza',
    folder = 'greeting';
