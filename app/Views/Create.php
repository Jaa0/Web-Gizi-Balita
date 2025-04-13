<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Analisis Gizi Balita</title>
    <link href="/css/main.css" rel="stylesheet">
</head>

<body class="bg-[#FFFEFE]">
    <section class="flex flex-col items-center justify-center h-auto">
        <div class="text-[#356036] text-center gap-10 space-y-0 lg:space-y-2 mt-5">
            <h1 class="text-[32px] lg:text-5xl font-bold">Analisis Gizi Balita</h1>
            <p class="text-[18px] lg:text-2xl">Dapatkan hasil analisis gizi yang optimal</p>
        </div>
        <div class="border-solid border-[#D8E8D8] rounded-[10px] border-2 p-5 lg:p-8 mt-5 pt-4">
            <?= form_open('submit', ['class' => 'flex flex-col text-[#356036] text-[18px]', 'id' => 'toddlerForm', 'novalidate' => true]) ?>

            <!-- NIK Field -->
            <div class="flex flex-col space-y-2">
                <label for="nik" class="text-base lg:text-[18px] font-bold">Nomor Induk Kependudukan (NIK)</label>
                <?= form_input([
                    'name' => 'nik',
                    'id' => 'nik',
                    'value' => set_value('nik'),
                    'class' => 'w-[320px] lg:w-[420px] h-10 lg:h-11 border-solid border-2 rounded-[10px] border-[#356036] pl-2'
                ]) ?>
                <div style="color: red;" class="text-sm lg:text-[18px]">
                    <?= isset($validation) ? $validation->getError('nik') : '' ?>
                </div>
            </div>

            <!-- Nama Ibu Kandung Field -->
            <div class="flex flex-col space-y-2 mt-5">
                <label for="namaIbu" class="text-base lg:text-[18px] font-bold">Nama Ibu Kandung</label>
                <?= form_input([
                    'name' => 'namaIbu',
                    'id' => 'namaIbu',
                    'value' => set_value('namaIbu'),
                    'class' => 'w-[320px] lg:w-[420px] h-10 lg:h-11 border-solid border-2 rounded-[10px] border-[#356036] pl-2'
                ]) ?>
                <div style="color: red;" class="text-sm lg:text-[18px]">
                    <?= isset($validation) ? $validation->getError('namaIbu') : '' ?>
                </div>
            </div>

            <!-- Nama Anak Field -->
            <div class="flex flex-col space-y-2 mt-5">
                <label for="nama" class="text-base lg:text-[18px] font-bold">Nama Anak</label>
                <?= form_input([
                    'name' => 'nama',
                    'id' => 'nama',
                    'value' => set_value('nama'),
                    'class' => 'w-[320px] lg:w-[420px] h-10 lg:h-11 border-solid border-2 rounded-[10px] border-[#356036] pl-2'
                ]) ?>
                <div style="color: red;" class="text-sm lg:text-[18px]">
                    <?= isset($validation) ? $validation->getError('nama') : '' ?>
                </div>
            </div>

            <!-- Date of Birth Field -->
            <div class="flex flex-col space-y-2 mt-5">
                <label for="birthdate" class="text-base lg:text-[18px] font-bold">Tanggal Lahir</label>
                <?= form_input([
                    'name' => 'birthdate',
                    'id' => 'birthdate',
                    'type' => 'date',
                    'value' => set_value('birthdate'),
                    'class' => 'w-[320px] lg:w-[420px] h-10 lg:h-11 border-solid border-2 rounded-[10px] border-[#356036] pl-2',
                    'onchange' => 'calculateAge()',
                    'max' => date('Y-m-d')
                ]) ?>
                <div style="color: red;" class="text-sm lg:text-[18px]">
                    <?= isset($validation) ? $validation->getError('birthdate') : '' ?>
                </div>
            </div>

            <!-- Age Fields (Auto-calculated) -->
            <div class="space-y-2 mt-5">
                <label class="text-base lg:text-[18px] font-bold">Umur</label>
                <div class="flex text-center items-center space-x-2">
                    <?= form_input([
                        'name' => 'age_year',
                        'id' => 'age_year',
                        'value' => set_value('age_year'),
                        'readonly' => true,
                        'class' => 'h-10 w-[40px] border-solid border-2 rounded-[10px] border-[#356036] box-border text-center flex items-center justify-center'
                    ]) ?>
                    <label for="age_year" class="text-base lg:text-[18px]">Tahun</label>

                    <?= form_input([
                        'name' => 'age_month',
                        'id' => 'age_month',
                        'value' => set_value('age_month'),
                        'readonly' => true,
                        'class' => 'h-10 w-[40px] border-solid border-2 rounded-[10px] border-[#356036] box-border text-center flex items-center justify-center'
                    ]) ?>
                    <label for="age_month" class="text-base lg:text-[18px]">Bulan</label>

                    <?= form_input([
                        'name' => 'age_days',
                        'id' => 'age_days',
                        'value' => set_value('age_days'),
                        'readonly' => true,
                        'class' => 'h-10 w-[40px] border-solid border-2 rounded-[10px] border-[#356036] box-border text-center flex items-center justify-center'
                    ]) ?>
                    <label for="age_days" class="text-base lg:text-[18px]">Hari</label>
                </div>
            </div>

            <!-- Gender Radio Buttons -->
            <div class="flex flex-col space-y-2 mt-5">
                <label class="text-base lg:text-[18px] font-bold">Gender</label>
                <div class="flex space-x-4">
                    <div class="flex items-center space-x-2">
                        <?= form_radio([
                            'name' => 'gender',
                            'id' => 'gender_male',
                            'value' => 'Pria',
                            'class' => 'w-6 h-6 cursor-pointer'
                        ]) ?>
                        <label for="gender_male" class="text-base lg:text-[18px]">Pria</label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <?= form_radio([
                            'name' => 'gender',
                            'id' => 'gender_female',
                            'value' => 'Wanita',
                            'class' => 'w-6 h-6 cursor-pointer'
                        ]) ?>
                        <label for="gender_female" class="text-base lg:text-[18px]">Wanita</label>
                    </div>
                </div>
                <div style="color: red;" class="text-sm lg:text-[18px]">
                    <?= isset($validation) ? $validation->getError('gender') : '' ?>
                </div>
            </div>

            <!-- Height Field -->
            <div class="flex flex-col space-y-2 mt-5">
                <label for="height" class="text-base lg:text-[18px] font-bold">Tinggi Badan (cm)</label>
                <?= form_input([
                    'name' => 'height',
                    'id' => 'height',
                    'value' => set_value('height'),
                    'class' => 'w-[320px] lg:w-[420px] h-10 lg:h-11 border-solid border-2 rounded-[10px] border-[#356036] pl-2'
                ]) ?>
                <div style="color: red;" class="text-sm lg:text-[18px]">
                    <?= isset($validation) ? $validation->getError('height') : '' ?>
                </div>
            </div>

            <!-- Weight Field -->
            <div class="flex flex-col space-y-2 mt-5">
                <label for="weight" class="text-base lg:text-[18px] font-bold">Berat Badan (kg)</label>
                <?= form_input([
                    'name' => 'weight',
                    'id' => 'weight',
                    'value' => set_value('weight'),
                    'class' => 'w-[320px] lg:w-[420px] h-10 lg:h-11 border-solid border-2 rounded-[10px] border-[#356036] pl-2'
                ]) ?>
                <div style="color: red;" class="text-sm lg:text-[18px]">
                    <?= isset($validation) ? $validation->getError('weight') : '' ?>
                </div>
            </div>
        </div>
        <!-- Submit Button -->
        <div class="mt-1 p-2 flex justify-center">
            <?= form_submit([
                'name' => 'submit',
                'id' => 'submit',
                'value' => 'Submit',
                'class' => 'w-[200px] lg:w-[258px] h-[41px] lg:h-[61px] border-solid border-2 rounded-[10px] bg-[#356036] text-white text-base lg:text-[22px] font-bold cursor-pointer'
            ]) ?>
        </div>
        <?= form_close() ?>
    </section>

    <script>
        function calculateAge() {
            const birthdate = document.getElementById('birthdate').value;
            if (!birthdate) return;

            const today = new Date();
            const birthDateObj = new Date(birthdate);

            if (birthDateObj > today) {
                alert("Tanggal lahir tidak boleh lebih dari hari ini!");
                document.getElementById('birthdate').value = '';
                return;
            }

            let years = today.getFullYear() - birthDateObj.getFullYear();
            let months = today.getMonth() - birthDateObj.getMonth();
            let days = today.getDate() - birthDateObj.getDate();

            // If days are negative, adjust by getting the last month's total days
            if (days < 0) {
                months--;
                const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0); // Get last day of previous month
                days += prevMonth.getDate();
            }

            // If months are negative, adjust years and correct months
            if (months < 0) {
                years--;
                months += 12;
            }

            document.getElementById('age_year').value = years;
            document.getElementById('age_month').value = months;
            document.getElementById('age_days').value = days;
        }
    </script>
</body>

</html>