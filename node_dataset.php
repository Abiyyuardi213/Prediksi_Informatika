<?php
class Dataset {
    public $years = [];
    public $students = [];
    private $filename = 'dataset.json';

    public function loadData() {
        if (file_exists($this->filename)) {
            $jsonData = file_get_contents($this->filename);
            $data = json_decode($jsonData, true);
            foreach ($data['data'] as $entry) {
                $this->years[] = $entry['year'];
                $this->students[] = $entry['students'];
            }
        }
    }

    public function saveData() {
        $data = [];
        for ($i = 0; $i < count($this->years); $i++) {
            $data[] = [
                'year' => $this->years[$i],
                'students' => $this->students[$i]
            ];
        }
        file_put_contents($this->filename, json_encode(['data' => $data], JSON_PRETTY_PRINT));
    }

    public function addData($year, $students) {
        $this->years[] = $year;
        $this->students[] = $students;
    }

    public function getData() {
        $data = [];
        for ($i = 0; $i < count($this->years); $i++) {
            $data[] = [
                'year' => $this->years[$i],
                'students' => $this->students[$i]
            ];
        }
        return $data;
    }

    public function predict($yearToPredict) {
        $n = count($this->years);

        $meanX = array_sum($this->years) / $n;
        $meanY = array_sum($this->students) / $n;

        $numerator = 0;
        $denominator = 0;
        for ($i = 0; $i < $n; $i++) {
            $numerator += ($this->years[$i] - $meanX) * ($this->students[$i] - $meanY);
            $denominator += pow($this->years[$i] - $meanX, 2);
        }

        $b = $numerator / $denominator;
        $a = $meanY - ($b * $meanX);

        $predictedStudents = $a + ($b * $yearToPredict);

        return $predictedStudents;
    }

    public function displayData() {
        if (count($this->years) > 0) {
            for ($i = 0; $i < count($this->years); $i++) {
                echo "Tahun: " . $this->years[$i] . " - Jumlah Mahasiswa: " . $this->students[$i] . "<br>";
            }
        } else {
            echo "Tidak ada data yang tersedia.<br>";
        }
    }
}
?>
