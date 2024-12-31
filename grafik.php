<?php
require_once 'node_dataset.php';

$dataset = new Dataset();
$dataset->loadData();
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
<body class="bg-gray-100"></body>
    <div class="flex h-screen">
        <?php include 'includes/sidebar.php'; ?>

        <div class="ml-64 flex flex-col flex-grow"></div>
            <header class="bg-blue-700 text-white py-4 px-6 shadow-md"></header>
                <h1 class="text-2xl font-bold flex items-center"></h1>
                    <span class="material-icons-outlined mr-2">show_chart</span>
                    Grafik Perkembangan Pendaftaran Mahasiswa Baru
                </h1>
            </header>

            <div class="mt-4 p-6 flex-1"></div>
                <div class="bg-white shadow-md rounded-lg p-6"></div>
                    <h2 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">Grafik Perkembangan</h2>
                    <canvas id="developmentChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mengambil data tahun dan jumlah mahasiswa dari PHP
        const years = <?php echo json_encode($dataset->years); ?>;
        const students = <?php echo json_encode($dataset->students); ?>;

        // Menyusun data untuk grafik
        const labels = years;
        const data = students;

        const ctx = document.getElementById('developmentChart').getContext('2d');
        const developmentChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels, // Label grafik (tahun)
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: data, // Data jumlah mahasiswa
                    borderColor: 'rgb(75, 192, 192)', // Warna garis grafik
                    backgroundColor: 'rgba(75, 192, 192, 0.2)', // Warna latar belakang grafik
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
