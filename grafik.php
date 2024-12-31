<?php
require_once 'node_dataset.php';

$dataset = new Dataset();
$dataset->loadData();

$predictions = $dataset->predictMultipleYears(end($dataset->years), 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Perkembangan Pendaftaran Mahasiswa Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'includes/sidebar.php'; ?>

        <div class="ml-64 flex flex-col flex-grow">
            <header class="bg-blue-700 text-white py-4 px-6 shadow-md">
                <h1 class="text-2xl font-bold flex items-center">
                    <span class="material-icons-outlined mr-2">show_chart</span>
                    Grafik Perkembangan Pendaftaran Mahasiswa Baru
                </h1>
            </header>

            <div class="mt-4 p-6 flex-1">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">Grafik Perkembangan</h2>
                    <canvas id="developmentChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        const years = <?php echo json_encode($dataset->years); ?>;
        const students = <?php echo json_encode($dataset->students); ?>;

        const predictedYears = <?php echo json_encode(array_column($predictions, 'year')); ?>;
        const predictedStudents = <?php echo json_encode(array_column($predictions, 'students')); ?>;

        const allYears = years.concat(predictedYears);
        const allStudents = students.concat(predictedStudents);

        const ctx = document.getElementById('developmentChart').getContext('2d');
        const developmentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: allYears,
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: allStudents,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tahun'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Jumlah Mahasiswa'
                        },
                        min: 0
                    }
                }
            }
        });
    </script>
</body>
</html>
