<?php
// Start session
session_start();

// Set page variables
$page_title = "SUS Evaluation";
$base_url = "./";

// Check if user just submitted
$submitted = false;
$sus_score = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Calculate SUS score
    // Odd items (1,3,5,7,9): subtract 1 from user response
    // Even items (2,4,6,8,10): subtract user response from 5
    // Multiply sum by 2.5 to get score out of 100
    
    $q1 = isset($_POST['q1']) ? (int)$_POST['q1'] : 0;
    $q2 = isset($_POST['q2']) ? (int)$_POST['q2'] : 0;
    $q3 = isset($_POST['q3']) ? (int)$_POST['q3'] : 0;
    $q4 = isset($_POST['q4']) ? (int)$_POST['q4'] : 0;
    $q5 = isset($_POST['q5']) ? (int)$_POST['q5'] : 0;
    $q6 = isset($_POST['q6']) ? (int)$_POST['q6'] : 0;
    $q7 = isset($_POST['q7']) ? (int)$_POST['q7'] : 0;
    $q8 = isset($_POST['q8']) ? (int)$_POST['q8'] : 0;
    $q9 = isset($_POST['q9']) ? (int)$_POST['q9'] : 0;
    $q10 = isset($_POST['q10']) ? (int)$_POST['q10'] : 0;
    
    // Calculate score
    $odd_sum = ($q1 - 1) + ($q3 - 1) + ($q5 - 1) + ($q7 - 1) + ($q9 - 1);
    $even_sum = (5 - $q2) + (5 - $q4) + (5 - $q6) + (5 - $q8) + (5 - $q10);
    
    $sus_score = ($odd_sum + $even_sum) * 2.5;
    $submitted = true;
    
    // Save to file for record keeping
    $result_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'responses' => [
            'q1' => $q1, 'q2' => $q2, 'q3' => $q3, 'q4' => $q4, 'q5' => $q5,
            'q6' => $q6, 'q7' => $q7, 'q8' => $q8, 'q9' => $q9, 'q10' => $q10
        ],
        'score' => $sus_score
    ];
    
    // Save to results file
    $results_file = 'sus_results.json';
    $all_results = [];
    
    if (file_exists($results_file)) {
        $all_results = json_decode(file_get_contents($results_file), true);
    }
    
    $all_results[] = $result_data;
    file_put_contents($results_file, json_encode($all_results, JSON_PRETTY_PRINT));
    
    // Auto-save to CSV (Excel format)
    $csv_file = 'sus_results.csv';
    $file_handle = fopen($csv_file, 'w');
    
    // Write header
    fputcsv($file_handle, [
        'Timestamp', 'Q1', 'Q2', 'Q3', 'Q4', 'Q5', 'Q6', 'Q7', 'Q8', 'Q9', 'Q10',
        'SUS Score', 'Grade', 'Interpretation'
    ]);
    
    // Write all results
    foreach ($all_results as $res) {
        $score = $res['score'];
        if ($score >= 80.3) { $grade = "A"; $interpretation = "Excellent"; }
        elseif ($score >= 68) { $grade = "B"; $interpretation = "Good"; }
        elseif ($score >= 51) { $grade = "C"; $interpretation = "OK"; }
        elseif ($score >= 39) { $grade = "D"; $interpretation = "Poor"; }
        else { $grade = "F"; $interpretation = "Awful"; }
        
        fputcsv($file_handle, [
            $res['timestamp'],
            $res['responses']['q1'], $res['responses']['q2'], $res['responses']['q3'],
            $res['responses']['q4'], $res['responses']['q5'], $res['responses']['q6'],
            $res['responses']['q7'], $res['responses']['q8'], $res['responses']['q9'],
            $res['responses']['q10'],
            $res['score'], $grade, $interpretation
        ]);
    }
    
    fclose($file_handle);
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            
            <?php if (!$submitted): ?>
                <!-- SUS Questionnaire -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="bi bi-clipboard-check"></i> System Usability Scale (SUS) Evaluation</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> Please rate your agreement with each statement on a scale from 1 (Strongly Disagree) to 5 (Strongly Agree).
                        </div>
                        
                        <form method="POST" action="">
                            
                            <!-- Question 1 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">1. I think that I would like to use this system frequently.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q1" id="q1_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q1_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q1" id="q1_2" value="2">
                                        <label class="btn btn-outline-primary" for="q1_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q1" id="q1_3" value="3">
                                        <label class="btn btn-outline-primary" for="q1_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q1" id="q1_4" value="4">
                                        <label class="btn btn-outline-primary" for="q1_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q1" id="q1_5" value="5">
                                        <label class="btn btn-outline-primary" for="q1_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 2 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">2. I found the system unnecessarily complex.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q2" id="q2_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q2_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q2" id="q2_2" value="2">
                                        <label class="btn btn-outline-primary" for="q2_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q2" id="q2_3" value="3">
                                        <label class="btn btn-outline-primary" for="q2_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q2" id="q2_4" value="4">
                                        <label class="btn btn-outline-primary" for="q2_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q2" id="q2_5" value="5">
                                        <label class="btn btn-outline-primary" for="q2_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 3 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">3. I thought the system was easy to use.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q3" id="q3_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q3_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q3" id="q3_2" value="2">
                                        <label class="btn btn-outline-primary" for="q3_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q3" id="q3_3" value="3">
                                        <label class="btn btn-outline-primary" for="q3_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q3" id="q3_4" value="4">
                                        <label class="btn btn-outline-primary" for="q3_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q3" id="q3_5" value="5">
                                        <label class="btn btn-outline-primary" for="q3_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 4 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">4. I think that I would need the support of a technical person to be able to use this system.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q4" id="q4_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q4_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q4" id="q4_2" value="2">
                                        <label class="btn btn-outline-primary" for="q4_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q4" id="q4_3" value="3">
                                        <label class="btn btn-outline-primary" for="q4_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q4" id="q4_4" value="4">
                                        <label class="btn btn-outline-primary" for="q4_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q4" id="q4_5" value="5">
                                        <label class="btn btn-outline-primary" for="q4_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 5 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">5. I found the various functions in this system were well integrated.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q5" id="q5_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q5_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q5" id="q5_2" value="2">
                                        <label class="btn btn-outline-primary" for="q5_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q5" id="q5_3" value="3">
                                        <label class="btn btn-outline-primary" for="q5_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q5" id="q5_4" value="4">
                                        <label class="btn btn-outline-primary" for="q5_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q5" id="q5_5" value="5">
                                        <label class="btn btn-outline-primary" for="q5_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 6 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">6. I thought there was too much inconsistency in this system.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q6" id="q6_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q6_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q6" id="q6_2" value="2">
                                        <label class="btn btn-outline-primary" for="q6_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q6" id="q6_3" value="3">
                                        <label class="btn btn-outline-primary" for="q6_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q6" id="q6_4" value="4">
                                        <label class="btn btn-outline-primary" for="q6_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q6" id="q6_5" value="5">
                                        <label class="btn btn-outline-primary" for="q6_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 7 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">7. I would imagine that most people would learn to use this system very quickly.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q7" id="q7_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q7_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q7" id="q7_2" value="2">
                                        <label class="btn btn-outline-primary" for="q7_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q7" id="q7_3" value="3">
                                        <label class="btn btn-outline-primary" for="q7_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q7" id="q7_4" value="4">
                                        <label class="btn btn-outline-primary" for="q7_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q7" id="q7_5" value="5">
                                        <label class="btn btn-outline-primary" for="q7_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 8 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">8. I found the system very cumbersome to use.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q8" id="q8_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q8_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q8" id="q8_2" value="2">
                                        <label class="btn btn-outline-primary" for="q8_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q8" id="q8_3" value="3">
                                        <label class="btn btn-outline-primary" for="q8_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q8" id="q8_4" value="4">
                                        <label class="btn btn-outline-primary" for="q8_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q8" id="q8_5" value="5">
                                        <label class="btn btn-outline-primary" for="q8_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 9 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">9. I felt very confident using the system.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q9" id="q9_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q9_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q9" id="q9_2" value="2">
                                        <label class="btn btn-outline-primary" for="q9_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q9" id="q9_3" value="3">
                                        <label class="btn btn-outline-primary" for="q9_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q9" id="q9_4" value="4">
                                        <label class="btn btn-outline-primary" for="q9_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q9" id="q9_5" value="5">
                                        <label class="btn btn-outline-primary" for="q9_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Question 10 -->
                            <div class="card mb-3">
                                <div class="card-body">
                                    <p class="fw-bold">10. I needed to learn a lot of things before I could get going with this system.</p>
                                    <div class="d-flex justify-content-between">
                                        <small>Strongly Disagree</small>
                                        <small>Strongly Agree</small>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="q10" id="q10_1" value="1" required>
                                        <label class="btn btn-outline-primary" for="q10_1">1</label>
                                        
                                        <input type="radio" class="btn-check" name="q10" id="q10_2" value="2">
                                        <label class="btn btn-outline-primary" for="q10_2">2</label>
                                        
                                        <input type="radio" class="btn-check" name="q10" id="q10_3" value="3">
                                        <label class="btn btn-outline-primary" for="q10_3">3</label>
                                        
                                        <input type="radio" class="btn-check" name="q10" id="q10_4" value="4">
                                        <label class="btn btn-outline-primary" for="q10_4">4</label>
                                        
                                        <input type="radio" class="btn-check" name="q10" id="q10_5" value="5">
                                        <label class="btn btn-outline-primary" for="q10_5">5</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Submit Evaluation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Results Display -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0"><i class="bi bi-check-circle"></i> Evaluation Complete!</h3>
                    </div>
                    <div class="card-body text-center">
                        <h1 class="display-1 text-primary"><?php echo number_format($sus_score, 1); ?></h1>
                        <h4>SUS Score (out of 100)</h4>
                        
                        <div class="mt-4">
                            <?php
                            // Interpret score
                            if ($sus_score >= 80.3) {
                                $grade = "A";
                                $interpretation = "Excellent";
                                $color = "success";
                            } elseif ($sus_score >= 68) {
                                $grade = "B";
                                $interpretation = "Good";
                                $color = "primary";
                            } elseif ($sus_score >= 51) {
                                $grade = "C";
                                $interpretation = "OK";
                                $color = "warning";
                            } elseif ($sus_score >= 39) {
                                $grade = "D";
                                $interpretation = "Poor";
                                $color = "danger";
                            } else {
                                $grade = "F";
                                $interpretation = "Awful";
                                $color = "danger";
                            }
                            ?>
                            
                            <div class="alert alert-<?php echo $color; ?> d-inline-block">
                                <h2 class="mb-0">Grade: <?php echo $grade; ?></h2>
                                <p class="mb-0"><?php echo $interpretation; ?></p>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Score Interpretation:</h5>
                            <ul class="list-unstyled">
                                <li>80.3+ = A (Excellent)</li>
                                <li>68-80.2 = B (Good)</li>
                                <li>51-67.9 = C (OK)</li>
                                <li>39-50.9 = D (Poor)</li>
                                <li>0-38.9 = F (Awful)</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <p><strong>Average SUS Score: 68</strong></p>
                            <p>Your score of <strong><?php echo number_format($sus_score, 1); ?></strong> is 
                            <?php echo $sus_score > 68 ? 'above' : ($sus_score == 68 ? 'equal to' : 'below'); ?> 
                            the average.</p>
                        </div>
                        
                        <div class="mt-4">
                            <a href="sus_evaluation.php" class="btn btn-primary">Take Another Evaluation</a>
                            <a href="sus_results.php" class="btn btn-info">View All Results</a>
                            <a href="index.php" class="btn btn-secondary">Back to Home</a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">Results automatically saved to: sus_results.json and sus_results.csv</small>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
