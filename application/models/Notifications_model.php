<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notifications_model extends Base_model
{
    protected $table = 'notifications';
    protected $single_for_view = 'Notificaciones';
    protected $primary_key = 'notification_id';
    protected $timestamp = false;
    protected $order = [];
    protected $column_order = ['notification_id', 'notification_created_at', 'notification_title', 'notification_observation'];
    protected $column_search = [];

    public function __construct()
    {
        parent::__construct();
        $this->datatable_customs_link = function ($row) {
            return $this->customLinks($row, $this->session->userdata('user_rol_label'));
        };
    }

    function customLinks($row, $rol)
    {
        return '<a href="' . site_url('notifications/form/' . $row['notification_id']) . '" class="btn btn-primary" title="Ver detalle"><span class="fa fa-eye"></span></a>';
    }

    public function notification_created_at_attribute($value, $row)
    {
        $col = '';
        $col .= '<span class="hidden-xs">' . date('d-m-Y', strtotime($value)) . ' </span> ';
        $col .= date('G:i', strtotime($value));
        return $col;
    }

    public function get_filters()
    {
        return [];
    }

    public function getNotificationTitle ($notificationId){
        $this->load->model(['Notifications_model']);
        $notification_model = new Notifications_model();
        $notification = $notification_model->get_by_id($notificationId);
        return $notification['notification_title'];
    }

    public function addNewDetail($notificationId = false, $importId, $notificationDetailObservation, $notificationTitle = '')
    {
        $this->load->model(['Notifications_model', 'NotificationDetails_model', 'Imports_model']);
        $notification_model = new Notifications_model();
        $notificationDetails_model = new NotificationDetails_model();
        $importsModel = new Imports_model();
        $notificationHeaderDoesntExist = empty($notificationId);
        $mustCreateNotificationHeader = $notificationHeaderDoesntExist;
        if ($mustCreateNotificationHeader) {
            $thereIsNoNotificationTitle = empty($notificationTitle);
            if ($thereIsNoNotificationTitle) {
                $importRow = $importsModel->get_where(['import_id' => $importId])[0];
                $importRef = $importRow['import_ref'];
                $notificationTitle = "Importación ID '" . $importId . "' con el referente '" . $importRef . "'";
            }
            $notificationId = $notification_model->createImportNotification($importId, $notificationTitle, '');
        }
        $notificationDetails_model->createNotificationDetail($notificationId, $notificationDetailObservation);
        return $notificationId;
    }

    public function createImportNotification($importId, $title, $observation)
    {
        $this->load->model('Notifications_model');
        $notification_model = new Notifications_model();
        $notification = [
            "notification_import" => $importId,
            "notification_title" => $title,
            "notification_observation" => $observation,
        ];
        $notificationId = $notification_model->save($notification);
        return $notificationId;
    }

    public function createNotification($title, $observation)
    {
        $this->load->model('Notifications_model');
        $notification_model = new Notifications_model();
        $notification = [
            "notification_title" => $title,
            "notification_observation" => $observation,
        ];
        $notificationId = $notification_model->save($notification);
        return $notificationId;
    }

    public function get_by_id($id)
    {
        $result = $this->db->get_where($this->table, [$this->primary_key => $id]);
        return !empty($result) ? $result->row_array() : [];
    }
}
