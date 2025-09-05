<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_holidays_20231109143649 extends CI_Migration
{

  public function up()
  {
    $this->db->query("
    CREATE TABLE IF NOT EXISTS `holidays` (
      `holiday_id` int(11) NOT NULL,
      `holiday_date` DATE NOT NULL,
      `holiday_detail` varchar(150) NOT NULL,
      `holiday_type` ENUM('immovable', 'portable', 'bridge') NOT NULL DEFAULT 'immovable',
      `holiday_created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

    $this->db->query("
    ALTER TABLE `holidays`
      ADD PRIMARY KEY (`holiday_id`);
    ");

    $this->db->query("
    ALTER TABLE `holidays`
      MODIFY `holiday_id` int(11) NOT NULL AUTO_INCREMENT;
    ");

    // 2019
    $this->db->query("
    INSERT INTO `holidays` (holiday_date, holiday_detail, holiday_type) VALUES
    ('2019-01-01', 'Año Nuevo', 'immovable'),
    ('2019-03-04', 'Carnaval', 'immovable'),
    ('2019-03-05', 'Carnaval', 'immovable'),
    ('2019-03-24', 'Día Nacional de la Memoria por la Verdad y la Justicia', 'immovable'),
    ('2019-04-02', 'Día del Veterano y de los Caídos en la Guerra de Malvinas', 'immovable'),
    ('2019-04-19', 'Viernes Santo Festividad Cristiana', 'immovable'),
    ('2019-05-01', 'Día del Trabajador', 'immovable'),
    ('2019-05-25', 'Día de la Revolución de Mayo', 'immovable'),
    ('2019-06-17', 'Paso a la Inmortalidad del Gral. Don Martín Güemes', 'portable'),
    ('2019-06-20', 'Paso a la Inmortalidad del General Manuel Belgrano', 'immovable'),
    ('2019-07-08', 'Feriado Puente Turístico', 'bridge'),
    ('2019-07-09', 'Día de la Independencia', 'immovable'),
    ('2019-08-17', 'Paso a la Inmortalidad del General José de San Martín', 'portable'),
    ('2019-08-19', 'Feriado Puente Turístico', 'bridge'),
    ('2019-10-12', 'Día del Respeto a la Diversidad Cultural', 'portable'),
    ('2019-10-14', 'Feriado Puente Turístico', 'bridge'),
    ('2019-11-06', 'Día del empleado bancario', 'immovable'),
    ('2019-11-10', 'Día de la Soberanía Nacional', 'portable'),
    ('2019-12-08', 'Inmaculada Concepción de María', 'immovable'),
    ('2019-12-25', 'Navidad', 'immovable');
    ");

    // 2020
    $this->db->query("
    INSERT INTO `holidays` (holiday_date, holiday_detail, holiday_type) VALUES
    ('2020-01-01', 'Año Nuevo', 'immovable'),
    ('2020-02-24', 'Carnaval', 'immovable'),
    ('2020-02-25', 'Carnaval', 'immovable'),
    ('2020-03-23', 'Feriado Puente Turístico', 'bridge'),
    ('2020-03-24', 'Día Nacional de la Memoria por la Verdad y la Justicia', 'immovable'),
    ('2020-03-31', 'Día del Veterano y de los Caídos en la Guerra de Malvinas', 'immovable'),
    ('2020-04-19', 'Viernes Santo Festividad Cristiana', 'immovable'),
    ('2020-05-01', 'Día del Trabajador', 'immovable'),
    ('2020-05-25', 'Día de la Revolución de Mayo', 'immovable'),
    ('2020-06-15', 'Paso a la Inmortalidad del Gral. Don Martín Güemes', 'portable'),
    ('2020-06-20', 'Paso a la Inmortalidad del General Manuel Belgrano', 'immovable'),
    ('2020-07-09', 'Día de la Independencia', 'immovable'),
    ('2020-07-10', 'Feriado Puente Turístico', 'bridge'),
    ('2020-08-17', 'Paso a la Inmortalidad del General José de San Martín', 'portable'),
    ('2020-10-12', 'Día del Respeto a la Diversidad Cultural', 'portable'),
    ('2019-11-06', 'Día del empleado bancario', 'immovable'),
    ('2020-11-23', 'Día de la Soberanía Nacional', 'portable'),
    ('2020-12-07', 'Feriado Puente Turístico', 'bridge'),
    ('2020-12-08', 'Inmaculada Concepción de María', 'immovable'),
    ('2020-12-25', 'Navidad', 'immovable');
    ");

    // 2021
    $this->db->query("
    INSERT INTO `holidays` (holiday_date, holiday_detail, holiday_type) VALUES
    ('2021-01-01', 'Año Nuevo', 'immovable'),
    ('2021-02-15', 'Carnaval', 'immovable'),
    ('2021-02-16', 'Carnaval', 'immovable'),
    ('2021-03-24', 'Día Nacional de la Memoria por la Verdad y la Justicia', 'immovable'),
    ('2021-04-02', 'Día del Veterano y de los Caídos en la Guerra de Malvinas', 'immovable'),
    ('2021-04-02', 'Viernes Santo Festividad Cristiana', 'immovable'),
    ('2021-05-01', 'Día del Trabajador', 'immovable'),
    ('2021-05-24', 'Feriado Puente Turístico', 'bridge'),
    ('2021-05-25', 'Día de la Revolución de Mayo', 'immovable'),
    ('2021-06-20', 'Paso a la Inmortalidad del General Manuel Belgrano', 'immovable'),
    ('2021-06-21', 'Paso a la Inmortalidad del Gral. Don Martín Güemes', 'portable'),
    ('2021-07-09', 'Día de la Independencia', 'immovable'),
    ('2021-08-16', 'Paso a la Inmortalidad del General José de San Martín', 'portable'),
    ('2021-10-08', 'Feriado Puente Turístico', 'bridge'),
    ('2021-10-11', 'Día del Respeto a la Diversidad Cultural', 'portable'),
    ('2019-11-06', 'Día del empleado bancario', 'immovable'),
    ('2021-11-20', 'Día de la Soberanía Nacional', 'portable'),
    ('2021-11-22', 'Feriado Puente Turístico', 'bridge'),
    ('2021-12-08', 'Inmaculada Concepción de María', 'immovable'),
    ('2021-12-25', 'Navidad', 'immovable');
    ");

    // 2022
    $this->db->query("
    INSERT INTO `holidays` (holiday_date, holiday_detail, holiday_type) VALUES
    ('2022-01-01', 'Año Nuevo', 'immovable'),
    ('2022-02-28', 'Carnaval', 'immovable'),
    ('2022-03-01', 'Carnaval', 'immovable'),
    ('2022-03-24', 'Día Nacional de la Memoria por la Verdad y la Justicia', 'immovable'),
    ('2022-04-02', 'Día del Veterano y de los Caídos en la Guerra de Malvinas', 'immovable'),
    ('2022-04-15', 'Viernes Santo Festividad Cristiana', 'immovable'),
    ('2022-05-01', 'Día del Trabajador', 'immovable'),
    ('2022-05-25', 'Día de la Revolución de Mayo', 'immovable'),
    ('2022-06-17', 'Paso a la Inmortalidad del Gral. Don Martín Güemes', 'portable'),
    ('2022-06-20', 'Paso a la Inmortalidad del General Manuel Belgrano', 'immovable'),
    ('2022-07-09', 'Día de la Independencia', 'immovable'),
    ('2022-08-15', 'Paso a la Inmortalidad del General José de San Martín', 'portable'),
    ('2022-10-07', 'Feriado Puente Turístico', 'bridge'),
    ('2022-10-10', 'Día del Respeto a la Diversidad Cultural', 'portable'),
    ('2019-11-06', 'Día del empleado bancario', 'immovable'),
    ('2022-11-20', 'Día de la Soberanía Nacional', 'portable'),
    ('2022-11-21', 'Feriado Puente Turístico', 'bridge'),
    ('2022-12-08', 'Inmaculada Concepción de María', 'immovable'),
    ('2022-12-09', 'Feriado Puente Turístico', 'bridge'),
    ('2022-12-25', 'Navidad', 'immovable');
    ");

    // 2023
    $this->db->query("
    INSERT INTO `holidays` (holiday_date, holiday_detail, holiday_type) VALUES
    ('2023-01-01', 'Año Nuevo', 'immovable'),
    ('2023-02-20', 'Carnaval', 'immovable'),
    ('2023-02-21', 'Carnaval', 'immovable'),
    ('2023-03-24', 'Día Nacional de la Memoria por la Verdad y la Justicia', 'immovable'),
    ('2023-04-02', 'Día del Veterano y de los Caídos en la Guerra de Malvinas', 'immovable'),
    ('2023-04-07', 'Viernes Santo Festividad Cristiana', 'immovable'),
    ('2023-05-01', 'Día del Trabajador', 'immovable'),
    ('2023-05-25', 'Día de la Revolución de Mayo', 'immovable'),
    ('2023-05-26', 'Feriado Puente Turístico', 'bridge'),
    ('2023-06-17', 'Paso a la Inmortalidad del Gral. Don Martín Güemes', 'portable'),
    ('2023-06-19', 'Feriado Puente Turístico', 'bridge'),
    ('2023-06-20', 'Paso a la Inmortalidad del General Manuel Belgrano', 'immovable'),
    ('2023-07-09', 'Día de la Independencia', 'immovable'),
    ('2023-08-21', 'Paso a la Inmortalidad del General José de San Martín', 'portable'),
    ('2023-10-13', 'Feriado Puente Turístico', 'bridge'),
    ('2023-10-16', 'Día del Respeto a la Diversidad Cultural', 'portable'),
    ('2019-11-06', 'Día del empleado bancario', 'immovable'),
    ('2023-11-20', 'Día de la Soberanía Nacional', 'portable'),
    ('2023-12-08', 'Inmaculada Concepción de María', 'immovable'),
    ('2023-12-25', 'Navidad', 'immovable');
    ");
  }

  public function down()
  {
  }
}
