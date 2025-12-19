<?php
// Start session
session_start();

// Set page variables
$page_title = "SUS Results";
$base_url = "./";
$is_logged_in = isset($_SESSION['doctor_id']);

// Read results file
$results_file = 'sus_results.json';
$results = [];

if (file_exists($results_file)) {
    $results = json_decode(file_get_contents($results_file), true);
}

// Calculate statistics if we have results
$total_count = count($results);
$average_score = 0;
$scores = [];

if ($total_count > 0) {
    foreach ($results as $result) {
        $scores[] = $result['score'];
    }
    $average_score = array_sum($scores) / $total_count;
    $min_score = min($scores);
    $max_score = max($scores);
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-bar-chart"></i> SUS Evaluation Results</h2>
                <a href="sus_evaluation.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Evaluation
                </a>
            </div>
            
            <?php if ($total_count > 0): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Results are automatically saved to:
                    <strong>sus_results.json</strong> and <strong>sus_results.csv</strong> in the ehr-system folder.
                </div>
                <!-- Statistics Summary -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-primary"><?php echo $total_count; ?></h3>
                                <p class="mb-0">Total Evaluations</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-success"><?php echo number_format($average_score, 1); ?></h3>
                                <p class="mb-0">Average Score</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-info"><?php echo number_format($max_score, 1); ?></h3>
                                <p class="mb-0">Highest Score</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-warning"><?php echo number_format($min_score, 1); ?></h3>
                                <p class="mb-0">Lowest Score</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Results Table -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">All Evaluation Results</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date & Time</th>
                                        <th>SUS Score</th>
                                        <th>Grade</th>
                                        <th>Interpretation</th>
                                        <th>Questions (Q1-Q10)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $counter = 1;
                                    foreach ($results as $result): 
                                        $score = $result['score'];
                                        
                                        // Calculate grade
                                        if ($score >= 80.3) {
                                            $grade = "A";
                                            $interpretation = "Excellent";
                                            $badge_color = "success";
                                        } elseif ($score >= 68) {
                                            $grade = "B";
                                            $interpretation = "Good";
                                            $badge_color = "primary";
                                        } elseif ($score >= 51) {
                                            $grade = "C";
                                            $interpretation = "OK";
                                            $badge_color = "warning";
                                        } elseif ($score >= 39) {
                                            $grade = "D";
                                            $interpretation = "Poor";
                                            $badge_color = "danger";
                                        } else {
                                            $grade = "F";
                                            $interpretation = "Awful";
                                            $badge_color = "danger";
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo $counter++; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($result['timestamp'])); ?></td>
                                            <td><strong><?php echo number_format($score, 1); ?></strong></td>
                                            <td><span class="badge bg-<?php echo $badge_color; ?>"><?php echo $grade; ?></span></td>
                                            <td><?php echo $interpretation; ?></td>
                                            <td>
                                                <small>
                                                    <?php 
                                                    echo implode(', ', [
                                                        $result['responses']['q1'],
                                                        $result['responses']['q2'],
                                                        $result['responses']['q3'],
                                                        $result['responses']['q4'],
                                                        $result['responses']['q5'],
                                                        $result['responses']['q6'],
                                                        $result['responses']['q7'],
                                                        $result['responses']['q8'],
                                                        $result['responses']['q9'],
                                                        $result['responses']['q10']
                                                    ]);
                                                    ?>
                                                </small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Interpretation Guide -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Score Interpretation Guide</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Grade Scale:</h6>
                                <ul class="list-unstyled">
                                    <li>✅ <strong>80.3+</strong> = Grade A (Excellent)</li>
                                    <li>🟢 <strong>68-80.2</strong> = Grade B (Good)</li>
                                    <li>🟡 <strong>51-67.9</strong> = Grade C (OK)</li>
                                    <li>🟠 <strong>39-50.9</strong> = Grade D (Poor)</li>
                                    <li>🔴 <strong>0-38.9</strong> = Grade F (Awful)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Benchmark:</h6>
                                <p><strong>Average SUS Score: 68</strong></p>
                                <p>Your average score of <strong><?php echo number_format($average_score, 1); ?></strong> is 
                                <?php 
                                if ($average_score > 68) {
                                    echo '<span class="text-success">above average ✅</span>';
                                } elseif ($average_score == 68) {
                                    echo '<span class="text-primary">at the average</span>';
                                } else {
                                    echo '<span class="text-warning">below average</span>';
                                }
                                ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- No Results Yet -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Evaluation Results Yet</h4>
                        <p class="text-muted">Complete the SUS evaluation to see results here.</p>
                        <a href="sus_evaluation.php" class="btn btn-primary">
                            <i class="bi bi-clipboard-check"></i> Take SUS Evaluation
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
