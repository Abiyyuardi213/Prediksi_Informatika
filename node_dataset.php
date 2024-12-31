<?php
class Dataset {
    public $years = [];
    public $students = [];
    private $filename = 'dataset.json';

    public function loadData() {
        if (file_exists($this->filename)) {
            $jsonData = file_get_contents($this->filename);
            $data = json_decode($jsonData, true);
            if (isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $entry) {
                    if (isset($entry['year']) && isset($entry['students'])) {
                        $this->years[] = $entry['year'];
                        $this->students[] = $entry['students'];
                    }
                }
            } else {
                echo "Data tidak valid atau kosong dalam dataset.json.";
            }
        } else {
            echo "File dataset.json tidak ditemukan.";
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
        $this->saveData(); // Save data to dataset.json after adding new data
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
        if ($n == 0) {
            return 0; // Avoid division by zero
        }
    
        $meanX = array_sum($this->years) / $n;
        $meanY = array_sum($this->students) / $n;
    
        $numerator = 0;
        $denominator = 0;
        for ($i = 0; $i < $n; $i++) {
            $numerator += ($this->years[$i] - $meanX) * ($this->students[$i] - $meanY);
            $denominator += pow($this->years[$i] - $meanX, 2);
        }
    
        if ($denominator == 0) {
            return $meanY; // Avoid division by zero
        }
    
        $b = $numerator / $denominator;
        $a = $meanY - ($b * $meanX);
    
        $predictedStudents = $a + ($b * $yearToPredict);
    
        return $predictedStudents;
    }

    public function predictMultipleYears($startYear, $numYears) {
        $predictions = [];
        for ($i = 0; $i < $numYears; $i++) {
            $predictions[] = [
                'year' => $startYear + $i,
                'students' => $this->predict($startYear + $i)
            ];
        }
        return $predictions;
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
