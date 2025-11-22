<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test API - WCB System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 24px;
            font-size: 32px;
        }
        
        h2 {
            color: #34495e;
            margin: 24px 0 16px;
            font-size: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid #ecf0f1;
        }
        
        pre {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 16px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }
        
        .status.success {
            background: #d5f4e6;
            color: #27ae60;
        }
        
        .status.error {
            background: #fadbd8;
            color: #e74c3c;
        }
        
        .test-section {
            margin-bottom: 32px;
        }
        
        .count {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 8px;
        }
        
        hr {
            border: none;
            border-top: 1px solid #ecf0f1;
            margin: 32px 0;
        }
        
        .back-link {
            display: inline-block;
            padding: 12px 24px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 24px;
            transition: all 0.2s;
        }
        
        .back-link:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }
        
        p {
            margin: 8px 0;
            color: #34495e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test API Endpoints - WCB System</h1>
        
        <?php
        require_once 'config.php';
        
        try {
            // Test 1: Get all TVs
            echo '<div class="test-section">';
            echo '<h2>1. Get All TVs</h2>';
            $tvs = getTVsByDepartment();
            echo '<span class="status success">✓ Success</span>';
            echo '<div class="count">Tổng số TV: ' . count($tvs) . '</div>';
            echo '<pre>' . json_encode($tvs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div>';
            
            // Test 2: Get all boards
            echo '<div class="test-section">';
            echo '<h2>2. Get All Boards</h2>';
            $boards = getAllBoards();
            echo '<span class="status success">✓ Success</span>';
            echo '<div class="count">Tổng số WCB: ' . count($boards) . '</div>';
            echo '<pre>' . json_encode($boards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div>';
            
            // Test 3: Get active assignments
            echo '<div class="test-section">';
            echo '<h2>3. Get Active Assignments</h2>';
            $assignments = getAllActiveAssignments();
            echo '<span class="status success">✓ Success</span>';
            echo '<div class="count">Tổng số assignment: ' . count($assignments) . '</div>';
            echo '<pre>' . json_encode($assignments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div>';
            
            // Test 4: Check TV limits
            echo '<div class="test-section">';
            echo '<h2>4. Check TV Limits</h2>';
            echo '<span class="status success">✓ Success</span>';
            echo '<pre>';
            foreach ($tvs as $tv) {
                $count = getTVBoardCount($tv['id']);
                $maxWCB = ($tv['code'] === 'BASEMENT_TV1') ? 3 : 1;
                $canAssign = canAssignToTV($tv['id']);
                echo "TV: {$tv['name']}\n";
                echo "  - Code: {$tv['code']}\n";
                echo "  - Current: {$count}/{$maxWCB} WCB\n";
                echo "  - Can assign: " . ($canAssign ? 'Yes' : 'No') . "\n\n";
            }
            echo '</pre>';
            echo '</div>';
            
            // Test 5: Database connection
            echo '<div class="test-section">';
            echo '<h2>5. Database Connection</h2>';
            $conn = getDBConnection();
            if ($conn->ping()) {
                echo '<span class="status success">✓ Connected</span>';
                echo '<pre>';
                echo "Host: " . DB_HOST . "\n";
                echo "Database: " . DB_NAME . "\n";
                echo "Status: Connected\n";
                echo '</pre>';
            } else {
                echo '<span class="status error">✗ Connection failed</span>';
            }
            echo '</div>';
            
            // Test 6: Test assign (if available)
            if (!empty($boards) && !empty($tvs)) {
                echo '<div class="test-section">';
                echo '<h2>6. Test Assign Function</h2>';
                $test_board = $boards[0];
                $test_tv = $tvs[0];
                
                echo "<p>Trying to assign Board '<strong>{$test_board['event_title']}</strong>' to TV '<strong>{$test_tv['name']}</strong>'...</p>";
                
                $result = assignBoardToTV($test_board['id'], $test_tv['id']);
                if ($result['success']) {
                    echo '<span class="status success">✓ ' . $result['message'] . '</span>';
                } else {
                    echo '<span class="status error">✗ ' . $result['message'] . '</span>';
                }
                echo '<pre>' . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<span class="status error">✗ Error: ' . $e->getMessage() . '</span>';
        }
        ?>
        
        <hr>
        <p style="color: #27ae60; font-weight: 500;">✓ All tests completed!</p>
        <a href="admin.php" class="back-link">← Quay lại Admin</a>
    </div>
</body>
</html>
