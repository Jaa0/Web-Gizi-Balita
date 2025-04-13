<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hasil Analisis</title>
    <link href="/css/main.css" rel="stylesheet">
</head>

<body class="bg-[#FFFEFE]">
    <section class="flex flex-col items-center justify-center h-auto">
        <div class="text-[#356036] text-center gap-10 space-y-2 mt-5">
            <h1 class="text-[32px] lg:text-5xl font-bold">Hasil Analisis Gizi Balita</h1>
        </div>
        <div class="flex flex-col mt-10">
            <div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
                <!-- Prediction Result -->
                <div class="flex flex-col border-solid bg-[#D8E8D8] border-[#356036] rounded-[10px] border-2 p-8 pt-4 w-[420px] lg:w-[720px] h-[400px] justify-center items-center gap-10">
                    <h1 class="text-2xl lg:text-3xl font-bold">Hasil Analisis</h1>
                    <img src="<?= $image ?>" alt="Gambar1" class="h-[200px]">
                    <p class="text-[14px] lg:text-2xl font-bold"><?= $nutrition_status ?> | <?= $weight_category ?> | <?= $height_category ?></p>
                </div>
                <!-- Data Balita -->
                <div class="flex flex-col border-solid bg-[#D8E8D8] border-[#356036] rounded-[10px] border-2 p-8 pt-4 w-[420px] h-[400px] items-center">
                    <h2 class="text-2xl lg:text-3xl font-bold mb-4 ">Data Balita</h2>
                    <div class="flex-col justify-between w-full">
                        <div class="space-y-2">
                            <div>
                                <h2 class="text-lg lg:text-xl font-bold">Nama:</h2>
                                <p class="text-lg lg:text-xl"><?= $nama ?></p>
                            </div>
                            <div>
                                <h2 class="text-lg lg:text-xl font-bold">Umur:</h2>
                                <p class="text-lg lg:text-xl"><?= $age_years ?> Tahun <?= $age_months ?> Bulan <?= $age_days ?> Hari</p>
                            </div>
                            <div>
                                <h2 class="text-lg lg:text-xl font-bold">Gender:</h2>
                                <p class="text-lg lg:text-xl"><?= $gender ?></p>
                            </div>
                            <div>
                                <h2 class="text-lg lg:text-xl font-bold">Tinggi Badan:</h2>
                                <p class="text-lg lg:text-xl"><?= $height ?> cm</p>
                            </div>
                            <div>
                                <h2 class="text-lg lg:text-xl font-bold">Berat Badan:</h2>
                                <p class="text-lg lg:text-xl"><?= $weight ?> kg</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Advice Section -->
            <div class="flex flex-col w-[420px] lg:w-full border-solid bg-[#D8E8D8] border-[#356036] rounded-[10px] border-2 p-5 mt-5 mb-5 justify-center space-y-2">
                <p class="text-[18px] lg:text-2xl font-bold">Saran :</p>
                <ol class="list-disc list-inside w-[400px] lg:w-[1000px]">
                    <?php foreach ($advice as $advice): ?>
                        <li><?= $advice ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
    </section>
</body>

</html>