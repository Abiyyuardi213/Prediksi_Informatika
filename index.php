<?php
require_once 'node_dataset.php';

$dataset = new Dataset();
$dataset->loadData();

$predicted = null;
$year = null;
$predictions = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_data'])) {
        $year = intval($_POST['year']);
        $students = intval($_POST['students']);
        $dataset->addData($year, $students);
        $dataset->saveData(); // Ensure data is saved after adding new data
    } elseif (isset($_POST['calculate_regression'])) {
        $year = intval($_POST['year']);
        $predicted = $dataset->predict($year + 1);
        $dataset->saveData();
    } elseif (isset($_POST['predict_next_5_years'])) {
        $year = intval($_POST['year']);
        $predictions = $dataset->predictMultipleYears($year + 1, 5);
        $dataset->saveData();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediksi Pendaftaran Mahasiswa Baru</title>
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
                    <span class="material-icons-outlined mr-2">school</span>
                    Prediksi Pendaftaran Mahasiswa Baru Teknik Informatika
                </h1>
            </header>

            <div class="mt-4 p-6 flex-1 flex">
                <div class="w-2/3 pr-6">
                    <form method="POST" action="" class="bg-white shadow-md rounded-lg p-6">
                        <div class="mb-4">
                            <label for="year" class="block text-gray-700 font-semibold mb-2">Tahun Pendaftaran</label>
                            <input type="number" id="year" name="year" required class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-200">
                        </div>

                        <div class="mb-4">
                            <label for="students" class="block text-gray-700 font-semibold mb-2">Jumlah Mahasiswa</label>
                            <input type="number" id="students" name="students" required class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-200">
                        </div>

                        <div class="mt-6 flex justify-between items-center">
                            <button type="submit" name="add_data" class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 shadow-lg transition">
                                <span class="material-icons-outlined mr-2">add_circle</span>
                                Tambah Data
                            </button>
                        </div>
                    </form>

                    <table class="mt-6 bg-white shadow-md rounded-lg w-full">
                        <thead>
                            <tr>
                                <th class="border px-4 py-2">Tahun</th>
                                <th class="border px-4 py-2">Jumlah Mahasiswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dataset->years) && !empty($dataset->students)): ?>
                                <?php foreach ($dataset->getData() as $entry): ?>
                                    <tr>
                                        <td class="border px-4 py-2"><?= $entry['year'] ?></td>
                                        <td class="border px-4 py-2"><?= $entry['students'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="border px-4 py-2 text-center">Data tidak tersedia.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <form method="POST" action="" class="mt-6">
                        <input type="hidden" name="year" value="<?= $year ?>">
                        <button type="submit" name="calculate_regression" class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 shadow-lg transition">
                            <span class="material-icons-outlined mr-2">calculate</span>
                            Hitung Regresi
                        </button>
                        <button type="submit" name="predict_next_5_years" class="inline-flex items-center bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 shadow-lg transition ml-4">
                            <span class="material-icons-outlined mr-2">timeline</span>
                            Prediksi 5 Tahun Kedepan
                        </button>
                    </form>

                    <?php if (!empty($predictions)): ?>
                        <table class="mt-6 bg-white shadow-md rounded-lg w-full">
                            <thead>
                                <tr>
                                    <th class="border px-4 py-2">Tahun</th>
                                    <th class="border px-4 py-2">Prediksi Jumlah Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($predictions as $prediction): ?>
                                    <tr>
                                        <td class="border px-4 py-2"><?= $prediction['year'] ?></td>
                                        <td class="border px-4 py-2"><?= round($prediction['students']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="w-1/3 bg-white shadow-md rounded-lg p-6">
                    <div class="sticky top-4">
                        <h2 class="text-xl font-bold text-gray-700 mb-4 border-b pb-2">Hasil Prediksi</h2>
                        <?php if ($predicted !== null): ?>
                            <div class="mb-6">
                                <p class="text-lg font-semibold text-gray-500">Prediksi Jumlah Mahasiswa pada Tahun <?= $year + 1 ?>:</p>
                                <p class="text-3xl font-bold text-green-700"><?= round($predicted) ?></p>
                            </div>
                        <?php endif; ?>
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Grafik Perkiraan Pendaftaran Mahasiswa</h3>
                            <?php if (!empty($dataset->years) && !empty($dataset->students)): ?>
                                <canvas id="predictionChart" width="400" height="200"></canvas>
                            <?php else: ?>
                                <p class="text-center text-red-500">Data tidak tersedia untuk menampilkan grafik.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const years = <?php echo json_encode($dataset->years); ?>;
        const students = <?php echo json_encode($dataset->students); ?>;

        const predictedYear = <?php echo $year ?? 0; ?> + 1;
        const predictedStudents = <?php echo $predicted !== null ? round($predicted) : 0; ?>;

        const labels = years.concat(predictedYear);
        const data = students.concat(predictedStudents);

        const ctx = document.getElementById('predictionChart').getContext('2d');
        const predictionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Mahasiswa',
                    data: data,
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
