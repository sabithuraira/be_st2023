<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;
use App\Models\PesSt2023;
use App\Models\SlsUmkm;
use App\Http\Controllers\Controller;

class TelegramController extends Controller
{
    public function pes_st2023(Request $request){
        //////////////FOR PRODUCTION
        $client = new \GuzzleHttp\Client();
        $update = json_decode($request->getContent());
        // Log::info('info request:', ['isi'=>$update]);
        $chatID = $update->message->chat->id;

        /////////////FOR TESTING
        // $message = "PES-1601052001000100-12-13-1";
        //PES-kode sls-jumlah RT tani-jumlah ART tani-selesai atau belum

        if(isset($update->message->text)){
            $message = $update->message->text;

            if(strtolower(str_replace(' ', '', $message))=='panduan'){
                $pesan = urlencode("Kirim laporan progres PL-KUMKM dengan format berikut: \n
                    <strong>KUMKM-IDSLS-Jumlah KK Sudah Cacah-NO Urut Usaha Terbesar-Jumlah Koperasi.</strong> Contoh: <pre>KUMKM-1691052001000100-12-13-1</pre> \n\n 
                    Kirim laporan progres PES dengan format berikut: \n
                    <strong>PES-IDSLS-Jumlah Ruta Tani-Jumlah ART Tani-Apakah sudah selesai SLS ini(jika selesai isi 1 jika tidak isi 0).</strong> Contoh: <pre>PES-1691052001000100-12-13-1</pre>");
            }
            else{
                $lower_msg = strtolower($message);
                $rincian_msg = explode("-", $lower_msg);
    
                if(count($rincian_msg)==5){
                    $msg_error = [];
                    
                    $id_sls = str_replace(' ', '', $rincian_msg[1]);
                            // $message = "PES-1601052001000100-12-13-1";
                            
                    $kd_prov = substr($id_sls, 0,2);
                    $kd_kab = substr($id_sls, 2,2);
                    $kd_kec = substr($id_sls, 4,3);
                    $kd_desa = substr($id_sls, 7,3);
                    $kd_sls = substr($id_sls, 10,4);
                    $kd_sub_sls = substr($id_sls, 14,2);

                    $kegiatan = str_replace(' ', '', $rincian_msg[0]);
                    if($kegiatan=="pes"){
                        $data = PesSt2023::where([
                            ['kode_prov', '=', $kd_prov],
                            ['kode_kab', '=', $kd_kab],
                            ['kode_kec', '=', $kd_kec],
                            ['kode_desa', '=', $kd_desa],
                            ['id_sls', '=', $kd_sls],
                            ['id_sub_sls', '=', $kd_sub_sls]
                        ])->first();
                                    
                        if($data==null){
                            $pesan = "Identitas SLS/Non SLS ini tidak ditemukan, silahkan perbaiki.";
                        }
                        else{
                            $jumlah_rt = str_replace(' ', '', $rincian_msg[2]);
                            $jumlah_art = str_replace(' ', '', $rincian_msg[3]);
                            $is_selesai = str_replace(' ', '', $rincian_msg[4]);
                        
                            if(!is_numeric($jumlah_rt)) $msg_error[] = "Isian 'Jumlah Ruta Tani' Harus Angka";
                            if(!is_numeric($jumlah_art)) $msg_error[] = "Isian 'Jumlah ART Tani' Harus Angka";
                            if(!is_numeric($is_selesai)) $msg_error[] = "Isian 'Informasi apakah selesai' Harus Angka";

                            if(count($msg_error)>0){
                                $pesan = "Error!! berikut rincian errornya : ".join(",", $msg_error);
                            }
                            else{
                                $data->jml_ruta_pes = $jumlah_rt;
                                $data->jml_art_pes = $jumlah_art;
                                $data->status_selesai = $is_selesai;
                                $data->save();
                                $pesan = "Sukses!! Laporan berhasil disimpan.";  
                            }
                        }
                    }
                    else if($kegiatan=="kumkm"){
                        $data = SlsUmkm::where([
                            ['kode_prov', '=', $kd_prov],
                            ['kode_kab', '=', $kd_kab],
                            ['kode_kec', '=', $kd_kec],
                            ['kode_desa', '=', $kd_desa],
                            ['id_sls', '=', $kd_sls],
                            ['id_sub_sls', '=', $kd_sub_sls]
                        ])->first();
                                    
                        if($data==null){
                            $pesan = "Identitas SLS/Non SLS ini tidak ditemukan, silahkan perbaiki.";
                        }
                        else{
                            $jumlah_kk_dicacah = str_replace(' ', '', $rincian_msg[2]);
                            $no_urut_usaha_terbesar = str_replace(' ', '', $rincian_msg[3]);
                            $jumlah_koperasi = str_replace(' ', '', $rincian_msg[4]);
                        
                            if(!is_numeric($jumlah_kk_dicacah)) $msg_error[] = "Isian 'Jumlah KK Dicacah' Harus Angka";
                            if(!is_numeric($no_urut_usaha_terbesar)) $msg_error[] = "Isian 'No Urut Usaha Terbesar' Harus Angka";
                            if(!is_numeric($jumlah_koperasi)) $msg_error[] = "Isian 'Jumlah Koperasi' Harus Angka";

                            if(count($msg_error)>0){
                                $pesan = "Error!! berikut rincian errornya : ".join(",", $msg_error);
                            }
                            else{
                                // 'jml_kk', 'no_urut_usaha_terbesar', 'jml_koperasi'
                                $data->jml_kk = $jumlah_kk_dicacah;
                                $data->no_urut_usaha_terbesar = $no_urut_usaha_terbesar;
                                $data->jml_koperasi = $jumlah_koperasi;
                                $data->save();
                                $pesan = "Sukses!! Laporan berhasil disimpan.";  
                            }
                        }
                    }
                    else{
                        $pesan = "Error!! Format pesan salah, silahkan ulangi."; 
                    }
                }
                else{
                    $pesan = "Format pesan anda salah. Balas pesan ini dengan pesan 'panduan' untuk bantuan format yang benar";  
                }
            }
        }
        else{
            $pesan = "Format pesan anda salah. Balas pesan ini dengan pesan 'panduan' untuk bantuan format yang benar";  
        }

        $API_message = "https://api.telegram.org/bot".env('TOKEN_SIMANTAP')."/sendmessage?chat_id=".$chatID."&text=".$pesan."&parse_mode=HTML";        
        $res = $client->get($API_message);

        return 1;
    }

    public function regsosek_belum_unduh(Request $request){
        $data = \App\RegsosekSls::where('photo_status_unduh', 0)
                    ->whereNotNull('photo_file_id')
                    ->paginate(100);

        return response()->json(['data'=>$data]);
    }

    public function regsosek_set_unduh(Request $request){
        $data_id = $request->data_id;
        $data = \App\RegsosekSls::find($data_id);
        if($data!=null){
            $data->photo_status_unduh = 1;
            $data->save();
            return response()->json(['msg'=>'Data berhasil disimpan']);
        }
        else{
            return response()->json(['msg'=>'Error, data tidak ditemukan']);
        }

    }
}
