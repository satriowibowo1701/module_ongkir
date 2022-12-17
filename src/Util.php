<?php

namespace Satrio\Scrapdata;

$GLOBALS["Log"] = require_once dirname(__FILE__) . "/Log.php";
class Util
{
    private static  $Curl;
    private static ?string $datatmp = NULL;
    private static $resdat = ["code" => 200, "data" => []];
    private static $i = 0;
    private static $repeat = false;
    private static $oldsearch;
    private static $url =  array("cekongkir" => ["cekresi" => "https://pluginongkoskirim.com/front/resi", "asal" => "https://pluginongkoskirim.com/front/asal?s=", "tujuan" => "https://pluginongkoskirim.com/front/tujuan?s=", "tarif" => "https://pluginongkoskirim.com/front/tarif"], "jt" => "https://jet.co.id/index/router/index.html", "rajongkir" => ["token" => "https://rajaongkir.com/", "req" => "https://rajaongkir.com/json/ongkirResult"]);
    private static $_defaulHeaders = array(
        'POST /front/resi HTTP/1.1',
        'Host: pluginongkoskirim.com',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        'Accept:application/json, text/plain, */*',
        'Content-Type: application/json',
        'Cookie: _ga_97ZTBFJHQ9=GS1.1.1670055196.1.0.1670055196.0.0.0; _fbp=fb.1.1670055196761.876631211; _ga=GA1.2.611357943.1670055197; _gid=GA1.2.1196658529.1670055197; __gads=ID=7d5ecb25f680f502-22af8691b9d80058:T=1670055197:RT=1670055197:S=ALNI_MapyjKOjpTnO5TMmDZuC9VedDoyLQ; __gpi=UID=00000b88adeb0ee9:T=1670055197:RT=1670055197:S=ALNI_MaprCAXzunsqL9tOgH49RYFFJGIBg; FCNEC=%5B%5B%22AKsRol_LCC_Zp_658oE4_20RD1hN_xCLIRe7ERT31JkMLkAsP7qnZ-RzXzgzYSLHYlrCIddx_qu6KlVDe1EdIPI9ugOITxgNgwj2lbq5hqScBd3R-j71rI5xpFr9iQUUbCimLz1WPbh4I35kU5g6AFDNk89FWaF0VA%3D%3D%22%5D%2Cnull%2C%5B%5D%5D',
        'Accept-Language: en-US,en;q=0.8,id;q=0.6,fr;q=0.4'
    );
    private static $_jtwilayah = array(
        'POST /index/router/index.html HTTP/2',
        'Host: jet.co.id',
        'accept: application/json, text/javascript, */*; q=0.01',
        'accept-language: en-US,en;q=0.9',
        'content-type: application/x-www-form-urlencoded; charset=UTF-8',
        'cookie: HWWAFSESID=4925773fb6c2b8081e; HWWAFSESTIME=1670083311838; think_var=en-us; PHPSESSID=1l39f4nduer315euae3frgtnv4; _gcl_au=1.1.1919027321.1670083313; _gid=GA1.3.62805522.1670083313; G_ENABLED_IDPS=google; _fbp=fb.2.1670083313625.1502598504; _gat_UA-236790491-1=1; _gat_gtag_UA_236790491_1=1; _ga=GA1.1.1172711214.1670083313; _ga_FNZN9DLGN1=GS1.1.1670083313.1.1.1670086017.0.0.0',
        'origin: https://jet.co.id',
        'referer: https://jet.co.id/rates',
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36',
        'x-requested-with: XMLHttpRequest',
        'x-simplypost-id: 8dd4576e0beb6900e8d422b8ac67b4d0',
        'x-simplypost-signature: b7c806caee77957caef5eb98eb7e66e5'
    );


    protected  static  function setupCurl($url, $data = null, $method = null, $option = null)
    {
        self::$Curl = curl_init();
        if ($method == null || $option == null) {
            $header = $url == self::$url["cekongkir"]["cekresi"] ? self::$_defaulHeaders : self::$_jtwilayah;
        }
        if ($method != null) {
            match ($method) {
                "POST" => self::SetupPost(),
                "GET" => self::SetupGet()
            };
            if ($option == 'tarif') {
                $header = array(
                    "POST /front/tarif HTTP/1.1",
                    'Accept: */*',
                    'Accept-Language: en-US,en;q=0.9',
                    'Connection: keep-alive',
                    'Content-Type: application/json',
                    'Host: pluginongkoskirim.com',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
                    'Referer: https://pluginongkoskirim.com/cek-tarif-ongkir/',
                );
            }
            curl_setopt(self::$Curl, CURLOPT_POSTFIELDS, $data);
        } else {
            self::SetupPost();
        }
        curl_setopt(self::$Curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt(self::$Curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(self::$Curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$Curl, CURLOPT_URL, $url);
    }
    /*
list courier :
jnt
wahana
tiki
trawlbens
sicepat
ncs
lion
jne
anteraja 
*/
    public static function TrackResi($noresi, $kurir)
    {
        self::setupCurl(self::$url["cekongkir"]["cekresi"],);
        $params = json_encode(array("kurir" =>  $kurir, "resi" => $noresi));
        curl_setopt(self::$Curl, CURLOPT_POSTFIELDS, $params);
        $res = curl_exec(self::$Curl);
        if (!$res) {
            $GLOBALS["Log"]->error("Error Execute CURL TrackResi");
        }
        curl_close(self::$Curl);
        $res =  json_decode($res, true);
        return self::ConvertResiToJson($res);
    }
    protected static function ConvertToJson($data, $option = null, $datasearch = null)
    {
        $log = $GLOBALS["Log"];

        $data = json_decode($data, true);
        if (isset($data["data"][0]["nama"])) {
            if ($data["data"][0]["nama"] == "") {
                $log->error('Gagal Data Null', ["Search" => $option, "Data" => $datasearch]);
                return json_encode(["code" => 404, "data" => null]);
            }
        }
        if ($data == false || $data == 'null') {
            $log->error('Gagal Data Null', ["Search" => $option, "Data" => $datasearch]);
            return json_encode(["code" => 404, "data" => null]);
        }
        if ($option != 'tarif') {
            if (is_string($err = self::IfEmpty(null, $data["error"]))) {
                return $err;
            }
            if (isset($data["jenis"])) {
                if ($data["jenis"] == 'Data tidak ditemukan') {
                    $log->error('Gagal Data Null', ["Search" => $option, "Data" => $datasearch]);
                    return json_encode(["code" => 404, "data" => null]);
                }
            }
        } else if ($option == 'tarif') {
            if (is_string($err = self::IfEmpty(IsExist: null, err: $data["error"]))) {
                return $err;
            }
            if (isset($data["message"])) {
                if ($data["message"] == 'Asal atau tujuan tidak ditemukan') {
                    $log->error('Gagal Data Null', ["Search" => $option, "Data" => $datasearch]);
                    return json_encode(["code" => 404, "data" => null]);
                }
            }
        }
        $res = ["code" => 200, "data" => []];
        foreach ($data["data"] as $val) {
            if ($option == 'asal') {
                unset($val["ekspedisi"]);
                array_push($res["data"], $val);
            } else if ($option == 'tujuan') {
                $jenis = $val["kabupaten_data"]["jenis"];
                unset($val["jumlah_kelurahan"], $val["kabupaten_id"], $val["provinsi_id"], $val["kabupaten_data"], $val["meta_data"]);
                $val["jenis"] = $jenis;
                array_push($res["data"], $val);
            } else if ($option == 'tarif') {
                unset($val["slug"]);
                array_push($res["data"], $val);
            }
        }
        $log->info("Berhasil", ["Search" => $option, "Data" => $datasearch]);
        return  json_encode($res);
    }
    public static function GetWilayahAsal($data)
    {
        $res = file_get_contents(self::$url["cekongkir"]["asal"] . $data);
        return self::ConvertToJson($res, 'asal');
    }

    public static function GetWilayahTujuan($data)
    {
        $res = file_get_contents(self::$url["cekongkir"]["tujuan"] . $data);
        return self::ConvertToJson($res, 'tujuan', $data);
    }

    public static function GetTarif($sender_name, $sender_id, $weight, $dest_name, $dest_id)
    {
        $data = "{\"asal_id\":$sender_id,\"asal\":\"$sender_name\",\"tujuan_id\":$dest_id,\"tujuan\":\"$dest_name\",\"berat\":$weight}";
        self::setupCurl(self::$url["cekongkir"]["tarif"], $data, "POST", 'tarif');
        $res = curl_exec(self::$Curl);
        curl_close(self::$Curl);
        return self::ConvertToJson($res, 'tarif');
    }

    protected static  function IfEmpty(mixed $IsExist = null, bool $err = false)
    {
        if (($IsExist == false && !is_null($IsExist)) || (!is_null($IsExist) && $IsExist != 200  && $IsExist != 20000) || $err == true) {
            return json_encode(["code" => 404, "data" => null]);
        }
    }
    public static  function  ConvertResiToJson($data): string
    {
        if (is_string($err = self::IfEmpty($data["data"]["found"]))) {
            $GLOBALS["Log"]->error("Convert Error TrackResi");
            return $err;
        }
        $first = $data["data"]['detail'];
        $second = $data["data"]['detail']["shipper"];
        $third = $data["data"]['detail']["consignee"];
        $res = [
            "status" => "200",
            "data" =>  [
                "noresi" => $first['code'],
                "service" => $first['service'],
                "asal_pengiriman" => $first['origin'],
                "tujuan_pengiriman" => $first['destination'],
                "current_status" => $first['status'],
                "pengirim" => ["nama" => $second["name"], "address" => $second["address"]],
                "penerima" => ["nama" => $third['name'], "alamat" => $third['address']],
                "current_position" => $first['current_position'],
                "history" => []
            ]
        ];
        $i = 0;
        foreach ($first["history"] as $data) {
            $res["data"]["history"][$i] = [
                "time" => $data["time"],
                "position" => $data["position"],
                "status" => $data["desc"]
            ];
            $i++;
        }

        return json_encode($res);
    }

    public static function ConvertWilayahJtToJson(mixed $data)
    {

        if (is_string($err = self::IfEmpty($data["code"]))) {
            $GLOBALS["Log"]->error("Error Convert WilayahJt");
            return ($err);
        }
        $res = ["code" => 200, "data" => []];
        foreach ($data["data"] as $val) {
            if (!in_array(["city" => $val["city"], "province" => $val["province"], "destcity" => $val["countyarea"]], $res["data"])) {
                array_push($res["data"], ["city" => $val["city"], "province" => $val["province"], "destcity" => $val["countyarea"]]);
            }
        }
        return json_encode($res);
    }

    public static function GetWilayahJT()
    {
        $data = "method=query%2FfindProCityArea&data=&pId=8dd4576e0beb6900e8d422b8ac67b4d0&pst=b7c806caee77957caef5eb98eb7e66e5";
        self::setupCurl(self::$url["jt"]);
        curl_setopt(self::$Curl, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec(self::$Curl);
        if (!$res) {
            $GLOBALS["Log"]->error("Error Execute CURL GetWilayahJT");
        }
        curl_close(self::$Curl);
        $res =  json_decode($res, true);
        $res = json_decode($res, true);
        return self::ConvertWilayahJtToJson($res);
    }
    public static function ConvertOngkirJTToJson(mixed $data)
    {
        if (is_string($err = self::IfEmpty($data["code"]))) return $err;
        $res = ["code" => 200, "data" => []];
        foreach ($data["data"] as $val) {
            array_push($res["data"], ["serviceType" => $val["serviceType"], "price" => $val["serviceFees"], "courier" => "J&T"]);
        }
        return json_encode($res);
    }
    public static function GetTarifJT($sender, $consignee, $weight)
    {
        $data = "method=query%2FfindRate&data%5BsenderAddr%5D=$sender&data%5BreceiverAddr%5D=$consignee&data%5Bweight%5D=$weight&pId=8dd4576e0beb6900e8d422b8ac67b4d0&pst=b7c806caee77957caef5eb98eb7e66e5";
        self::setupCurl(self::$url["jt"]);
        curl_setopt(self::$Curl, CURLOPT_POSTFIELDS, $data);
        $res = curl_exec(self::$Curl);
        if (!$res) {
            $GLOBALS["Log"]->error("Error Execute CURL GetTarifJT");
        }
        curl_close(self::$Curl);
        $res =  json_decode($res, true);
        $res = json_decode($res, true);
        return self::ConvertOngkirJTToJson($res);
    }

    public static function SetupGet()
    {
        curl_setopt(self::$Curl, CURLOPT_POST, 0);
        curl_setopt(self::$Curl, CURLOPT_HTTPGET, 1);
    }

    public static function SetupPost()
    {
        curl_setopt(self::$Curl, CURLOPT_POST, 1);
        curl_setopt(self::$Curl, CURLOPT_HTTPGET, 0);
    }


    public static function SetupCurlRajaOngkir($option, ?array $data = null)
    {
        self::$Curl = curl_init();
        if ($option == 'getwilayah') {
            self::SetUpGet();
            curl_setopt(self::$Curl, CURLOPT_URL, 'https://api.rajaongkir.com/starter/city');
            curl_setopt(self::$Curl, CURLOPT_HTTPHEADER, array('key:3ae37b376002a84e298dc44775a17d23'));
            curl_setopt(self::$Curl, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec(self::$Curl);
            curl_close(self::$Curl);
            return $res;
        } elseif ($option == 'getongkir') {
            self::SetupPost();
            $reqparams = "asal={$data["sender"]}&tujuan={$data["dest"]}&berat={$data["weight"]}&tipe=ongkir&kotaasal={$data["sender_id"]}&kotatujuan={$data["dest_id"]}&kurir=jne%3Atiki%3Apos%3Apcp%3Arpx%3Aesl&cari=Periksa+Ongkir";
            curl_setopt(self::$Curl, CURLOPT_URL, "https://rajaongkir.com/");
            curl_setopt(self::$Curl, CURLOPT_HEADER, 1);
            curl_setopt(self::$Curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt(self::$Curl, CURLOPT_POSTFIELDS, $reqparams);
            $res = curl_exec(self::$Curl);
            if (!$res) {
                $GLOBALS["Log"]->error("Error Execute CURL GetOngkirRajaOngkir");
            }
            curl_close(self::$Curl);
            preg_match('/^Location:\s+[^\s]*/mi', $res, $matches);
            preg_match('/([a-z0-9]+[^https:\/\/rajaongkir\.com\/hasil\/ongkir\?q\=].+)/im', $matches[0], $matchess);
            return $matchess[0];
        }
    }

    public static function GetWilayahRajaOngkir()
    {
        $data = self::SetupCurlRajaOngkir('getwilayah');
        if (!$data) {
            $GLOBALS["Log"]->error("Error Execute CURL GetWilayahRajaOngkir");
        }
        $res = json_decode($data, true);
        $status = $res["rajaongkir"]["status"]["code"];
        $data = $res["rajaongkir"]["results"];
        $res = ["status" => $status, "data" => []];
        foreach ($data as $arr) {
            unset($arr["type"]);
            array_push($res["data"], $arr);
        }
        $GLOBALS["Log"]->info("Berhasil GetWilayahRajaOngkir");
        return $res;
    }

    public static function GetongkirRajaOngkir($sender, $dest, $weight, $sender_id, $dest_id, mixed $kurirtype = 0, $option = null)
    {
        $kurirtype = ($option != null && $option > 0) ? $option : $kurirtype;
        if (self::$repeat && self::$oldsearch != [$sender_id, $dest_id]) {
            self::$datatmp = null;
        }
        if (self::$datatmp != null) {
            $matchess = self::$datatmp;
        } else {
            $matchess = self::SetupCurlRajaOngkir('getongkir', ["sender" => $sender, "dest" => $dest, "weight" => $weight, "sender_id" => $sender_id, "dest_id" => $dest_id]);
            self::$datatmp = $matchess;
        }
        self::$Curl = curl_init();
        if ($kurirtype == 'all') {
            $data2 = "q=$matchess&i=0";
        } else if (is_int($kurirtype)) {
            $data2 = "q=$matchess&i=$kurirtype";
        }
        curl_setopt(self::$Curl, CURLOPT_POST, 1);
        curl_setopt(self::$Curl, CURLOPT_URL, "https://rajaongkir.com/json/ongkirResult");
        curl_setopt(self::$Curl, CURLOPT_HTTPHEADER, array(
            'POST /json/ongkirResult HTTP/1.1',
            'Accept: */*',
            'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Cookie: _ga=GA1.2.1719113765.1670065087; rajaongkir_user_session=ADdXbQVjXTlSfVAkUW4ENQc2VmhTegJxVWYOfFsiVTMLagZmBAoGPgMxACJSOl5zUjwEMAk%2BU2lUd1RiV2hWYQJnVzBaZQ8zD2QGMANlDDwAYVdiBTNdYlIwUDNRYwRiBzNWZFNqAmdVYQ45WzNVOwszBjoENgZnA2QAIlI6XnNSPAQyCTxTaVR3VDlXLFYJAjVXMlo0D3IPZAZ7AyEMfwBtVyQFbF0yUjVQbVF2BDUHN1ZjU3YCO1U3DiFbaVVtCysGOgRlBmEDdwA7UnJeOlI3BDMJNlNxVCBUI1c5ViQCC1c3WjcPZQ9vBnwDcAxmACVXbQVnXTJSN1BtUXYESQdpVihTMQJuVW8Oblt%2BVW8LKwY4BHUGfwMCAGlSb15kUmkEdAl%2FU3NUG1QEV3xWZwJkV3haYA87DyEGXwM7DDMAYFdjBW1dI1J%2BUGFRYAQtByZWE1MoAnJVbw5qWwZVPwtnBkMEPAYjA3oANVIyXjdSKAQwCTpTc1R9VBtXFFYCAhlXGlp8DyAPbQZhAzkMOAB2VxAFM11gUm1QOFF9BCQHRVY6UyoCbVVuDmpbflVrCzUGPwR7BmcDewAwUi9eMFImBFAJbVM1VDRUIlc1VnkCYVdlWmcPLg8yBj4DcAxmACVXbQVnXTBSPFB1UTgEZQd1ViZTBwJjVWAOe1s4VSwLbAZ8BCwGdQNuAGlSO14xUjAENAk8U2FUZlRnV21WZgJiV21aIw86DzgGMgNwDCgAJVcyBSRdXFJiUDZRIARlByRWaVMrAjhVMw41W3NVeAs%2BBnU%3D; _gid=GA1.2.2094640807.1670237113; _gat=1',
            'Host: rajaongkir.com',
            'Origin: https://rajaongkir.com',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',
            'X-Requested-With: XMLHttpRequest',
        ));
        curl_setopt(self::$Curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$Curl, CURLOPT_POSTFIELDS, $data2);
        $res2 = curl_exec(self::$Curl);
        curl_close(self::$Curl);
        if (preg_match("/([error]+$)/im", $res2) && $kurirtype != "all"  && $option == null) {
            $GLOBALS["Log"]->error("Error getting data GetongkirRajaOngkir", ["Data" => ["sender" => $sender, "dest" => $dest]]);
            return json_encode(["code" => 404, "data" => null]);
        }
        $html = new \DOMDocument();
        $html->loadHTML($res2);
        $finder = new \DomXPath($html);
        $classname = "ro-result";
        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        if ($kurirtype != 'all' || $option != null) {
            $jenis2 = match ($kurirtype) {
                0 => 'JNE',
                1 => 'TIKI',
                2 => 'POS',
                3 => 'PCP',
                4 => 'RPX',
            };
        } else if ($kurirtype == 'all') {
            $jenis2 = "JNE";
        }
        if ($kurirtype != 'all' && $option == null) {
            $resdat = ["code" => 200, "data" => []];
            $i = 0;
        }
        if ($nodes->length > 0) :
            foreach ($nodes as $node) {
                if (preg_match("/^($jenis2|RP)+([\s]*)([0-9\.]*$)/im", $node->nodeValue) == false) {
                    if ($kurirtype == 'all' || $option != null) {
                        array_push(self::$resdat["data"], ["serviceType" => $node->nodeValue, "serviceFees" => null, "courier" => $jenis2]);
                    } else {
                        array_push($resdat["data"], ["serviceType" => $node->nodeValue, "serviceFees" => null, "courier" => $jenis2]);
                    }
                }
                if (preg_match("/[^\s]*[0-9]$/im", $node->nodeValue, $arr)) {
                    $node->nodeValue = str_replace('.', '', $node->nodeValue);
                    if ($kurirtype == 'all' || $option != null) {
                        self::$resdat["data"][self::$i]["serviceFees"] = $node->nodeValue;
                        self::$i++;
                    } else {
                        $resdat["data"][$i]["serviceFees"] = $node->nodeValue;
                    }
                }
            }
        endif;
        if ($kurirtype == 'all') {
            for ($j = 1; $j <= 4; $j++) {
                self::GetongkirRajaOngkir($sender, $dest, $weight, $sender_id, $dest_id, null, $j);
            }
            if (count(self::$resdat["data"]) == 0) {
                return json_encode(["code" => 404, "data" => null]);
            }
            self::$repeat = true;
            self::$oldsearch = [$sender_id, $dest_id];
            $res = self::$resdat;
            self::$resdat = ["code" => 200, "data" => []];
            self::$i = 0;
            return json_encode($res);
        } else if ($option == null && $kurirtype != 'all') {
            return json_encode($resdat);
        }
    }
}
