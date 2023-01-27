<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Location extends Model
{
    use HasFactory;

    // protected $table = 'location';
    
    // protected $locations;

    // public function __construct(array $attributes = [])
    // {
    //     parent::__construct($attributes);

    //     $this->$locations=(Http::get('https://genus.rdfmis.ph/StoreAPI/api/locations.php?token=8AFASbzK5OH0E9OuZF5LlI9qZo8fqr1X'))->object()->records;
    //     // $this->$locations = Location::hydrate( $locations );
    // }


    
    // $locations = (Http::get('https://genus.rdfmis.ph/StoreAPI/api/locations.php?token=8AFASbzK5OH0E9OuZF5LlI9qZo8fqr1X'))->object()->records;
    // $location_model = Location::hydrate( $locations );

    protected $attributes = array(["sample"=>"sa"]);


    // public static function locations(){
    //    return $location_model;
    // }

}
