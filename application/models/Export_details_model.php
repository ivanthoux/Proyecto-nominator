 <?php

defined('BASEPATH') or exit('No direct script access allowed');

class Export_details_model extends Base_model
{

    protected $table = 'export_details';
    protected $single_for_view = '';
    protected $primary_key = 'export_detail_id';
    protected $timestamp = false;
    protected $column_order = [];
    protected $column_search = [];

    public function __construct()
    {
        parent::__construct();
    }
}
