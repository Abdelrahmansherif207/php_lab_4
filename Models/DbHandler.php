<?php
interface DbHandler {
    public function connect();
    public function get_data($fields = array(),  $start = 0);
    public function disconnect();   
    public function get_record_by_id($id,$primary_key); 
    public function get_pages();
    public function search_data($searchTerm);
}