<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;

class TransaksiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function data()
    {
        $data = DB::table('tb_penjualan')->get();

        return $data;
    }

    public function simpan(Request $request)
    {
        $param = $request->json()->all();
        $penjualan = $param;

        $kode = DB::table('tb_penjualan')->max('id') + 1;
        $kode = 'PNJ/'.date('dmy').'/'.str_pad($kode, 5, 0, STR_PAD_LEFT);

        $penjualan['kode'] = $kode;
        $penjualan['status'] = '';
        $penjualan['created_at'] = date('Y-m-d H:i:s');

        unset($penjualan['penjualan_detil']);
        $transaksi = DB::table('tb_penjualan')->insertGetId($penjualan);

        $penjualan_detil = $param['penjualan_detil'];
        
        $count = count($penjualan_detil);
        for ($i=0; $i < $count; $i++) { 
            $penjualan_detil[$i]['id_penjualan'] = $transaksi;
            $penjualan_detil[$i]['kode_penjualan'] = $kode;
            $penjualan_detil[$i]['created_at'] = $penjualan['created_at'];
        }

        $transaksi_detail = DB::table('tb_penjualan_detail')->insert($penjualan_detil);

        if($transaksi_detail){
            $msg = array(
                'status'    => 'success'
            );
        }else{
            $msg = array(
                'status'    => 'failed'
            );            
        }

        return $msg;        
    }

}
