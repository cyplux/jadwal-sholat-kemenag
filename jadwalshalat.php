<?php

const BASE_URI_KEMENAG = 'https://bimasislam.kemenag.go.id/';

// Get cookies
// $cookieJar = array();
// $ch = curl_init(BASE_URI_KEMENAG . 'jadwalshalat');
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// // curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
// curl_exec($ch);
// curl_close($ch);

// Get HTML contents
$ch = curl_init(BASE_URI_KEMENAG . 'jadwalshalat');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
$html = curl_exec($ch);
curl_close($ch);

// Get provinsi
$provinsi = array();
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);
$options = $xpath->query("//select[@id='search_prov']/option");
foreach ($options as $option) {
    $value = $option->getAttribute('value');
    $text = $option->nodeValue;
    if ($text != "PUSAT") {
        $provinsi[] = array(
            "value" => $value,
            "text" => $text,
        );
    }
}

// list daftar provinsi
$dataProvinsi = array_column($provinsi, 'text');
$dataProvinsiString = implode(' - ', $dataProvinsi);
// ACEH - SUMATERA UTARA - SUMATERA BARAT - RIAU - KEPULAUAN RIAU - JAMBI - BENGKULU - SUMATERA SELATAN - KEPULAUAN BANGKA BELITUNG - LAMPUNG - BANTEN - JAWA BARAT - DKI JAKARTA - JAWA TENGAH - D.I. YOGYAKARTA - JAWA TIMUR - BALI - NUSA TENGGARA BARAT - NUSA TENGGARA TIMUR - KALIMANTAN BARAT - KALIMANTAN SELATAN - KALIMANTAN TENGAH - KALIMANTAN TIMUR - KALIMANTAN UTARA - GORONTALO - SULAWESI SELATAN - SULAWESI TENGGARA - SULAWESI TENGAH - SULAWESI UTARA - SULAWESI BARAT - MALUKU - MALUKU UTARA - PAPUA - PAPUA BARAT



// echo($dataProvinsiString);

$text_arr = array_column($provinsi, 'text');
$value_arr = array_column($provinsi, 'value');
$provinsiTeks = "JAWA TIMUR"; // ganti sesuai provinsi yang dimaui
$provinsiId = $value_arr[array_search($provinsiTeks, $text_arr)];


$ch = curl_init(BASE_URI_KEMENAG . '/ajax/getKabkoshalat');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('x' => $provinsiId));
$response = curl_exec($ch);
curl_close($ch);

$kabkot = array();
$dom = new DOMDocument();
@$dom->loadHTML($response);
$xpath = new DOMXPath($dom);
$options = $xpath->query("//option");
foreach ($options as $option) {
    $value = $option->getAttribute('value');
    $text = $option->nodeValue;
    $kabkot[] = array(
        "value" => $value,
        "text" => $text,
    );
}




$text_arr = array_column($kabkot, 'text');
$value_arr = array_column($kabkot, 'value');
$kabkotTeks = "KAB. BONDOWOSO"; // ganti sesuai provinsi dan kabupaten yang dimaui
$kabkotId = $value_arr[array_search($kabkotTeks, $text_arr)];

//list data kabupaten sesuai provinsi yang dipilih
//KAB. BANGKALAN - KAB. BANYUWANGI - KAB. BLITAR - KAB. BOJONEGORO - KAB. BONDOWOSO - KAB. GRESIK - KAB. JEMBER - KAB. JOMBANG - KAB. KEDIRI - KAB. LAMONGAN - KAB. LUMAJANG - KAB. MADIUN - KAB. MAGETAN - KAB. MALANG - KAB. MOJOKERTO - KAB. NGANJUK - KAB. NGAWI - KAB. PACITAN - KAB. PAMEKASAN - KAB. PASURUAN - KAB. PONOROGO - KAB. PROBOLINGGO - KAB. SAMPANG - KAB. SIDOARJO - KAB. SITUBONDO - KAB. SUMENEP - KAB. TRENGGALEK - KAB. TUBAN - KAB. TULUNGAGUNG - KOTA BATU - KOTA BLITAR - KOTA KEDIRI - KOTA MADIUN - KOTA MALANG - KOTA MOJOKERTO - KOTA PASURUAN - KOTA PROBOLINGGO - KOTA SURABAYA

$dataKabupaten = array_column($kabkot, 'text');
$dataKabupatenString = implode(' - ', $dataKabupaten);

// echo($dataKabupatenString);


//ambil jadwal per bulan

$ch = curl_init(BASE_URI_KEMENAG . '/ajax/getShalatbln');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
    'x' => $provinsiId,
    'y' => $kabkotId,
    'bln' => 1,
    'thn' => 2023,
));
$response = curl_exec($ch);
curl_close($ch);
$shalatbln = json_decode($response);


// echo "<pre>";
// var_dump($shalatbln);
echo '<h4>'.$shalatbln->kabko.'</h4>';
echo '<table>';
echo '<tr>';
echo '<th>Tanggal</th>';
echo '<th>Imsak</th>';
echo '<th>Subuh</th>';
echo '<th>Terbit</th>';
echo '<th>Dhuha</th>';
echo '<th>Dzuhur</th>';
echo '<th>Ashar</th>';
echo '<th>Maghrib</th>';
echo '<th>Isya</th>';
echo '</tr>';
foreach ($shalatbln->data as $data) {
    echo '<tr>';
    echo '<td>' . $data->tanggal . '</td>';
    echo '<td>' . $data->imsak . '</td>';
    echo '<td>' . $data->subuh . '</td>';
    echo '<td>' . $data->terbit . '</td>';
    echo '<td>' . $data->dhuha . '</td>';
    echo '<td>' . $data->dzuhur . '</td>';
    echo '<td>' . $data->ashar . '</td>';
    echo '<td>' . $data->maghrib . '</td>';
    echo '<td>' . $data->isya . '</td>';
    echo '</tr>';
}
echo '</table>';
