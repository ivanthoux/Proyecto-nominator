<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserActions_model
 *
 * @author harlyman
 */
class UserActions_model extends Base_model {

    protected $table = 'user_actions';
    protected $primary_key = 'useraction_id';
    protected $timestamp = false;

    public function getByEntityId($entity, $entity_id, $acction = false) {
        $this->db->select("*")
                ->where([
                    'useraction_entity' => $entity,
                    'useraction_entity_id' => $entity_id
        ]);
        if (!empty($acction)) {
            $this->db->where(['useraction_action' => $action]);
        }
        
        return $this->db->order_by('useraction_date', 'DESC')
                        ->limit(1)
                        ->get($this->table)
                        ->row_array();
    }

}
