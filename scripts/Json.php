<?php
/** Created by Anol Kr Ghosh */
class Json
{
    private $file = './../files/data.json', 
    $jsonstring = null, 
    $jsonarray = array(), 
    $jsonobj = null, 
    $data = array();

    public function __construct()
    {
        if (!file_exists($this->file)) {
            touch($this->file);
        }
    }

    private function read(){
        $this->jsonstring = file_get_contents($this->file);
        $this->jsonarray = empty($this->jsonstring) ? array() : json_decode($this->jsonstring, true);
        $this->jsonobj = (object) $this->jsonarray;
    }

    private function processdata($data,$collection=false)
    {
        $this->read();
        if($collection){
            if(empty($this->jsonarray) || $this->jsonarray == null && $this->jsonarray[$collection] == null){
                unset($this->jsonarray);
                $this->jsonarray = array();
                $this->jsonarray[$collection] = array($data);
                $this->jsonarray = json_encode($this->jsonarray);
                return $this->jsonarray;
            }elseif(property_exists($this->jsonobj , $collection)){
                // $this->jsonarray[$collection] = $data;
                array_push($this->jsonarray[$collection],$data);
                $this->jsonarray = json_encode($this->jsonarray);
                return $this->jsonarray;
            }else{
                $this->jsonarray[$collection] = array($data);
                $this->jsonarray = json_encode($this->jsonarray);
                return $this->jsonarray;
            }
            return false;
        }else{
            if(empty($this->jsonarray) || $this->jsonarray == null){
                unset($this->jsonarray);
                $this->jsonarray = array();
                $this->jsonarray = array($data);
                $this->jsonarray = json_encode($this->jsonarray);
                return $this->jsonarray;
            }else{
                $this->jsonarray[] = $data;
                $this->jsonarray = json_encode($this->jsonarray);
                return $this->jsonarray;
            }
            return false;
        }
        return false;


    }

    public function push($data, $collection = false)
    {
        if (is_array($data) && !empty($data) ) {
            if($collection){
                $this->read();
                $total_array = 0;
                $total_array += count($this->jsonarray) < 1 ? 1 : count($this->jsonarray);
                $total_collection_array = 0;
                if($total_array > 0 && property_exists($this->jsonobj , $collection)){
                    $total_collection_array += count($this->jsonarray[$collection]);
                }
                $total_collection_array += count(array($data));
                $prediction = $total_array +  $total_collection_array;
                file_put_contents($this->file, $this->processdata($data,$collection));
                $this->read();
                $total_array = 0;
                $total_array += count($this->jsonarray);
                $total_collection_array = 0;
                if($total_array > 0 && property_exists($this->jsonobj , $collection)){
                    $total_collection_array += count($this->jsonarray[$collection]);
                }
                $current = $total_array + $total_collection_array;
                if($prediction == $current){
                    return true;
                }
                return false;
            }else{
                $this->read();
                $total_array = count($this->jsonarray) < 1 ? 1 : count($this->jsonarray);
                $arrays_to_add = count(array($data));
                $prediction= $total_array +  $arrays_to_add;
                file_put_contents($this->file, $this->processdata($data,$collection));
                $this->read();
                $current = count($this->jsonarray);
                if($prediction == $current){
                    return true;
                }
                return false;
            }
            return false;
         
        }
        return false;
    }

    public function get($collection = false)
    {
        $this->read();
        if ($collection) {
            return json_encode($this->jsonarray[$collection]);
        }
        return json_encode($this->jsonarray);
    }
}
