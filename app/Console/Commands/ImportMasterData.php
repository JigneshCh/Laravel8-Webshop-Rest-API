<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;
use DB;

class ImportMasterData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:masterdata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customers and product from csv file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {		
        $logs = $this->importProductData(); 
		$info = $logs['total_imported']."/".$logs['total_data']." Product ata imported ";
		$this->info($info);
		
		$logs = $this->importCustomerData();
		$info = $logs['total_imported']."/".$logs['total_data']." Customer data imported ";
		$this->info($info);		
    }
	
	/**
     * Import Products from csv.
     *
     * @return array $log
     */
	public function importProductData()
    {
		$start_time = Carbon::now();
        $exist_products = Product::pluck('sku')->toArray();
		
		$file_path = storage_path('csvdata/products.csv'); 
		$file = fopen($file_path, 'r');
		$array = array(); $i = 0; $j = 0;
		
		while (($row = fgetcsv($file)) !== FALSE) {
		    if (!$i) {
				$i++;
				continue;
			}
			$sku = "PRO".$row[0];
			if(!in_array($sku,$exist_products)){
				$input = [
					'sku' => $sku,
					'productname' => $row[1],
					'price' => floatval($row[2]), 
				];
				Product::create($input);
				$j++;
			}
			$i++;
		}
		
		$log =  [
			'model'=>'products',
			'total_data'=>($i-1),
			'total_imported'=>$j,
			'start_time'=>$start_time,
			'end_time'=>Carbon::now()
		];		
		DB::table('master_data_logs')->insert($log);
		return $log;
    }
	
	/**
     * Import Customers from csv.
     *
     * @return array $log
     */
	public function importCustomerData()
    {
		$start_time = Carbon::now();
        $exist_email = Customer::pluck('email')->toArray();
		
		$file_path = storage_path('csvdata/customers.csv'); 
		$file = fopen($file_path, 'r');
		$array = array(); $i = 0; $j = 0;
		$customers = array();
		
		while (($row = fgetcsv($file)) !== FALSE) {
		    if (!$i) {
				$i++;
				continue;
			}
			
			if(!in_array($row[2],$exist_email)){
				$timestamp = Carbon::now();
				$customers[] = [
					'job_title' => $row[1],
					'email' => $row[2],
					'name' => $row[3],
					'registered_since' => $row[4],
					'phone' => $row[5],
					'created_at' => $timestamp, 
					'updated_at' => $timestamp, 
				];
				$j++;
			}
			$i++;
			
			if(count($customers) >= 1000){
				Customer::insert($customers);
				$customers = [];
			}
		}
		
		if(count($customers)){
			Customer::insert($customers);
		}
		
		$log =  [
			'model'=>'customers',
			'total_data'=>($i-1),
			'total_imported'=>$j,
			'start_time'=>$start_time,
			'end_time'=>Carbon::now()
		];		
		DB::table('master_data_logs')->insert($log);
		return $log;
    }
}

















