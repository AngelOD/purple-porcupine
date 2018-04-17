<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use SW802F18\Contracts\SensorCluster;
use App\Http\Controllers\Controller;

class SensorController extends Controller
{
    private $sensorCluster = null;
    
    public function __construct(SensorCluster $sc)
    {
        $this->sensorCluster = $sc;
        
    }
    /**
     * Updates the sensors
     */
    public function updateSensor()
    {
        dd($this->sensorCluster);
        
    }
}
?>