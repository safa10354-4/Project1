<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
//use phpseclib3\Crypt\Hash;
use Illuminate\Support\Facades\Hash;
class Superseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
     Admin::create([
         'name'=>'Ammarzyada',
         'email'=>'zyada@gmail.com',
         'password'=>Hash::make('ASzy##123'),
          'role'=>'1',
         'description_company'=>'ITE',
         'company_website'=>'Damascus',
         'image'=>'null',
         'type'=>'null'
     ]);


    }
}
