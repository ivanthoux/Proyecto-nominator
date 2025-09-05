<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_union_agreement_categories_20231015171455 extends CI_Migration
{
  public function up()
  {
    $this->db->query("
      CREATE TABLE `union_agreement_categories` (
        `unionagreementcategory_id` INT NOT NULL AUTO_INCREMENT , 
        `unionagreementcategory_type` varchar(100),
        `unionagreementcategory_category` varchar(100),
        PRIMARY KEY (`unionagreementcategory_id`)
        ) ENGINE = InnoDB;
      ");

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Administrativo',
      "unionagreementcategory_category" => 'A',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Administrativo',
      "unionagreementcategory_category" => 'B',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Administrativo',
      "unionagreementcategory_category" => 'C',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Administrativo',
      "unionagreementcategory_category" => 'D',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Administrativo',
      "unionagreementcategory_category" => 'E',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Administrativo',
      "unionagreementcategory_category" => 'F',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    // CAJEROS
    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Cajero',
      "unionagreementcategory_category" => 'A',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);
    
    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Cajero',
      "unionagreementcategory_category" => 'B',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);
    
    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Cajero',
      "unionagreementcategory_category" => 'C',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    // AUXILIAR
    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Auxiliar',
      "unionagreementcategory_category" => 'A',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);
    
    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Auxiliar',
      "unionagreementcategory_category" => 'B',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Auxiliar',
      "unionagreementcategory_category" => 'C',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);
    
    // AUXILIAR ESPECIALIZADO
    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Auxiliar especializado',
      "unionagreementcategory_category" => 'A',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Auxiliar especializado',
      "unionagreementcategory_category" => 'B',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);
    
    // VENDEDOR
    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Vendedor',
      "unionagreementcategory_category" => 'A',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Vendedor',
      "unionagreementcategory_category" => 'B',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Vendedor',
      "unionagreementcategory_category" => 'C',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);

    $unionAgreementCategory = [
      "unionagreementcategory_type" => 'Vendedor',
      "unionagreementcategory_category" => 'D',
    ];
    $this->db->insert("union_agreement_categories", $unionAgreementCategory);
  }

  public function down()
  {
  }
}
