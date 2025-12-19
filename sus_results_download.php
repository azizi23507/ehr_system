<?php
// SUS Results Download Handler
// Exports results in JSON or Excel format

$format = isset($_GET['format']) ? $_GET['format'] : 'json';
$results_file = 'sus_results.json';

// Check if results file exists
if (!file_exists($results_file)) {
    die("No results available yet. Please complete the SUS evaluation first.");
}

// Read results
$results = json_decode(file_get_contents($results_file), true);

if (empty($results)) {
    die("No results available.");
}

if ($format == 'json') {
    // Download as JSON
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="sus_results_' . date('Y-m-d') . '.json"');
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit;
    
} elseif ($format == 'excel') {
    // Download as Excel (CSV format - opens in Excel)
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sus_results_' . date('Y-m-d') . '.csv"');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Write header row
    fputcsv($output, [
        'Timestamp',
        'Q1', 'Q2', 'Q3', 'Q4', 'Q5', 'Q6', 'Q7', 'Q8', 'Q9', 'Q10',
        'SUS Score',
        'Grade',
        'Interpretation'
    ]);
    
    // Write data rows
    foreach ($results as $result) {
        // Calculate grade
        $score = $result['score'];
        if ($score >= 80.3) {
            $grade = "A";
            $interpretation = "Excellent";
        } elseif ($score >= 68) {
            $grade = "B";
            $interpretation = "Good";
        } elseif ($score >= 51) {
            $grade = "C";
            $interpretation = "OK";
        } elseif ($score >= 39) {
            $grade = "D";
            $interpretation = "Poor";
        } else {
            $grade = "F";
            $interpretation = "Awful";
        }
        
        fputcsv($output, [
            $result['timestamp'],
            $result['responses']['q1'],
            $result['responses']['q2'],
            $result['responses']['q3'],
            $result['responses']['q4'],
            $result['responses']['q5'],
            $result['responses']['q6'],
            $result['responses']['q7'],
            $result['responses']['q8'],
            $result['responses']['q9'],
            $result['responses']['q10'],
            $result['score'],
            $grade,
            $interpretation
        ]);
    }
    
    fclose($output);
    exit;
}
?>
