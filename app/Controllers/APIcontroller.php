<?php

namespace App\Controllers;

use CodeIgniter\Controller;
// use CodeIgniter\HTTP\ResponseInterface;
use DateTime;

class APIcontroller extends Controller
{
    public function create()
    {
        helper('form');

        $data = [
            'current_date' => date('Y-m-d'), // Current date in YYYY-MM-DD format
        ];

        return view('Create', $data);
    }

    public function submit()
    {
        helper(['form', 'url']);
        $validation = \Config\Services::validation();

        // Form validation rules
        $validation->setRules([
            'nik' => [
                'label' => 'Nomor Induk Kependudukan (NIK)',
                'rules' => 'required|exact_length[16]|numeric',
                'errors' => [
                    'required' => 'NIK harus diisi.',
                    'exact_length' => 'NIK harus terdiri dari 16 digit.',
                    'numeric' => 'NIK harus berupa angka.'
                ]
            ],
            'namaIbu' => [
                'label' => 'Nama Ibu Kandung',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Ibu harus diisi'
                ]
            ],
            'nama' => [
                'label' => 'Nama Balita',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama Balita harus diisi'
                ]
            ],
            'birthdate' => [
                'label' => 'Tanggal Lahir',
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Tanggal lahir harus diisi.',
                ]
            ],
            'gender' => [
                'label' => 'Gender',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Pilih salah satu gender.'
                ]
            ],
            'height' => [
                'label' => 'Tinggi Badan',
                'rules' => 'required|numeric|greater_than_equal_to[40]|less_than_equal_to[150]',
                'errors' => [
                    'required' => 'Tinggi badan harus diisi.',
                    'numeric' => 'Tinggi badan harus berupa angka.',
                    'greater_than_equal_to' => 'Tinggi badan minimal 40 cm.',
                    'less_than_equal_to' => 'Tinggi badan maksimal 150 cm.'
                ]
            ],
            'weight' => [
                'label' => 'Berat Badan',
                'rules' => 'required|numeric|greater_than_equal_to[3]|less_than_equal_to[50]',
                'errors' => [
                    'required' => 'Berat badan harus diisi.',
                    'numeric' => 'Berat badan harus berupa angka.',
                    'greater_than_equal_to' => 'Berat badan minimal 3 kg.',
                    'less_than_equal_to' => 'Berat badan maksimal 50 kg.'
                ]
            ],
        ]);

        // If validation fails
        if (!$validation->withRequest($this->request)->run()) {
            return view('Create', [
                'current_date' => date('Y-m-d'),
                'validation' => $validation
            ]);
        }

        // Get form data
        $nik = $this->request->getPost('nik');
        $namaIbu = $this->request->getPost('namaIbu');
        $nama = $this->request->getPost('nama');
        $birthdate = new DateTime($this->request->getPost('birthdate'));
        $gender = $this->request->getPost('gender');
        $height = $this->request->getPost('height');
        $weight = $this->request->getPost('weight');

        // Calculate age
        $today = new DateTime();
        $ageInterval = $today->diff($birthdate);
        $ageYears = $ageInterval->y;
        $ageMonths = $ageInterval->m;
        $ageDays = $ageInterval->d;

        // Prepare data for API
        $inputData = [
            'nik' => $nik,
            'birthdate' => $birthdate->format('Y-m-d'),
            'gender' => $gender,
            'height' => $height,
            'weight' => $weight
        ];

        // Send API request
        $apiUrl = 'http://127.0.0.1:5000/predict';
        $response = $this->callApi($apiUrl, $inputData);

        if ($response['status'] === 200) {
            $result = json_decode($response['body'], true);

            $nutritionData = [
                'nutrition_status' => $result['nutrition_status'] ?? 'Unknown',
                'weight_category' => $result['weight_category'] ?? 'Unknown',
                'height_category' => $result['height_category'] ?? 'Unknown',
                'nik' => $nik,
                'namaIbu' => $namaIbu,
                'nama' => $nama,
                'age_years' => $ageYears,
                'age_months' => $ageMonths,
                'age_days' => $ageDays,
                'gender' => $gender,
                'height' => $height,
                'weight' => $weight,
                'image' => $this->getImage($result['nutrition_status']),
                'advice' => $this->getAdvice($result['nutrition_status'])
            ];

            session()->setFlashdata('nutrition_data', $nutritionData);
            return redirect()->to('Result');
        } else {
            return "Error in API request: " . $response['status'];
        }
    }

    private function callApi($url, $data)
    {
        $client = \Config\Services::curlrequest();
        $response = $client->post($url, [
            'json' => $data
        ]);

        return [
            'status' => $response->getStatusCode(),
            'body' => $response->getBody()
        ];
    }

    private function getImage($nutritionStatus)
    {
        $images = [
            'Gizi Buruk' => '/assets/Buruk.jpg',
            'Gizi Kurang' => '/assets/Kurang.jpg',
            'Gizi Baik' => '/assets/Baik.jpg',
            'Gizi Lebih' => '/assets/Lebih.jpg',
            'Obesitas' => '/assets/Obesitas.png'
        ];

        return $images[$nutritionStatus] ?? '/assets/default.jpg';
    }

    private function getAdvice($nutritionStatus)
    {
        $advice = [
            'Gizi Buruk' => [
                "Segera ke fasilitas kesehatan, gizi buruk pada balita perlu penanganan dokter secepatnya.",
                "MP-ASI dengan isi piringku kaya protein hewani",
                "Pantau pertumbuhan dan perkembangan di Posyandu",
                "Lengkapi imunisasi",
                "Konsultasi intensif, balita gizi buruk memerlukan konsultasi rutin dengan dokter anak atau ahli gizi untuk mendapatkan panduan yang tepat dan pemantauan perkembangan secara berkala."
            ],
            'Gizi Kurang' => [
                "Segera ke fasilitas kesehatan.",
                "JMP-ASI dengan isi piringku kaya protein hewani",
                "Pantau pertumbuhan dan perkembangan di Posyandu",
                "Lengkapi imunisasi."
            ],
            'Gizi Baik' => [
                "MP-ASI dengan isi piringku kaya protein hewani.",
                "Pantau pertumbuhan dan perkembangan di Posyandu",
                "Lengkapi imunisasi."
            ],
            'Berisiko Gizi Lebih' => [
                "MP-ASI dengan isi piringku kaya protein hewani",
                "Pantau pertumbuhan dan perkembangan di Posyandu.",
                "Lengkapi imunisasi",
                "Aktivitas fisik seimbang, dorong anak untuk bermain aktif agar banyak bergerak"
            ],
            'Gizi Lebih' => [
                "MP-ASI dengan isi piringku kaya protein hewani.",
                "Pantau pertumbuhan dan perkembangan di Posyandu.",
                "Lengkapi imunisasi",
                "Aktivitas fisik sesuai usia, dorong anak untuk aktif bermain dengan mainan yang melibatkan gerakan fisik."
            ],
            'Obesitas' => [
                "Segera ke fasilitas kesehatan, obesitas pada balita perlu penanganan dokter untuk mencegah komplikasi kesehatan jangka panjang.",
                "MP-ASI dengan isi piringku kaya protein hewani.",
                "Pantau pertumbuhan dan perkembangan di Posyandu",
                "Lengkapi imunisasi",
                "Perbanyak aktivitas fisik, batasi waktu yang dihabiskan anak untuk duduk di depan layar atau perangkat elektronik. Ajak anak lebih banyak bergerak dan bermain."
            ]
        ];

        return $advice[$nutritionStatus] ?? ["Tidak ada saran untuk status gizi yang tidak diketahui."];
    }
    public function result()
    {
        $nutritionData = session()->get('nutrition_data');

        if (!$nutritionData) {
            return redirect()->to('Create')->with('error', 'No data available');
        }

        // Ensure correct data formatting
        $data = [
            'nik' => $nutritionData['nik'],
            'namaIbu' => $nutritionData['namaIbu'],
            'nama' => $nutritionData['nama'],
            'age_years' => intval($nutritionData['age_years'] ?? 0),
            'age_months' => intval($nutritionData['age_months'] ?? 0),
            'age_days' => intval($nutritionData['age_days'] ?? 0),
            'gender' => $nutritionData['gender'],
            'height' => $nutritionData['height'],
            'weight' => $nutritionData['weight'],
            'nutrition_status' => $nutritionData['nutrition_status'] ?? 'Unknown',
            'weight_category' => $nutritionData['weight_category'] ?? 'Unknown',
            'height_category' => $nutritionData['height_category'] ?? 'Unknown'
        ];

        $filePath = FCPATH . 'DataGiziBalita.xlsx';

        // Ensure the file exists or create a new spreadsheet
        if (file_exists($filePath)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
        } else {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Sheet1');

            // Add headers if creating a new file
            $headers = ['NIK', 'Nama Ibu', 'Nama Balita', 'Age (Years)', 'Age (Months)', 'Age (Days)', 'Gender', 'Height', 'Weight', 'Nutrition Status', 'Weight Category', 'Height Category'];
            $sheet->fromArray([$headers], null, 'A1');
        }

        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();
        $startRow = $highestRow + 1;

        // Write data to the next available row
        $values = array_values($data);
        $sheet->fromArray([$values], null, "A{$startRow}");

        // Set column A (NIK) to text format to prevent scientific notation
        $sheet->getStyle("A:A")->getNumberFormat()->setFormatCode('0');

        // Apply borders to all columns, including Weight & Height Category
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($values)); // Get last column letter
        $sheet->getStyle("A{$startRow}:{$columnLetter}{$startRow}")->applyFromArray($borderStyle);

        // Save the file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        return view('Result', $nutritionData);
    }
}
