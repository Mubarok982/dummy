<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skripsi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'operator') redirect('auth/login');
        $this->load->model('operator/M_skripsi_opt');
        $this->load->model('M_Log');
    }

    // --- PENUGASAN PEMBIMBING ---
    public function penugasan()
    {
        $data['title'] = 'Penugasan Pembimbing';
        $data['mahasiswa'] = $this->M_skripsi_opt->get_all_mahasiswa_skripsi();
        $data['dosen_list'] = $this->M_skripsi_opt->get_dosen_pembimbing_list();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_penugasan_pembimbing', $data);
        $this->load->view('template/footer');
    }
    
    public function assign_aksi()
    {
        $id_skripsi = $this->input->post('id_skripsi');
        $p1 = $this->input->post('pembimbing1');
        $p2 = $this->input->post('pembimbing2');
        
        $pembimbing1 = empty($p1) ? NULL : $p1;
        $pembimbing2 = empty($p2) ? NULL : $p2;

        if ($id_skripsi) {
            $this->M_skripsi_opt->assign_pembimbing($id_skripsi, $pembimbing1, $pembimbing2);
            $this->session->set_flashdata('pesan_sukses', 'Penugasan pembimbing berhasil.');
            $this->M_Log->record('Penugasan', 'Mengatur Pembimbing Skripsi ID: ' . $id_skripsi, $id_skripsi);
        } else {
             $this->session->set_flashdata('pesan_error', 'Gagal: ID Skripsi tidak ditemukan.');
        }
        redirect('operator/skripsi/penugasan');
    }

    // --- CEK PLAGIARISME ---
    public function plagiarisme()
    {
        $data['title'] = 'Cek Plagiarisme (Input Manual)';
        $data['plagiat_list'] = $this->M_skripsi_opt->get_plagiarisme_tasks();

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_cek_plagiat_list', $data);
        $this->load->view('template/footer');
    }

    public function verifikasi_plagiat($id_plagiat, $action)
    {
        $status = ($action == 'acc') ? 'Lulus' : 'Tolak';
        $this->M_skripsi_opt->update_plagiarisme_status($id_plagiat, $status);
        
        $this->M_Log->record('Plagiarisme', 'Verifikasi plagiat ID: ' . $id_plagiat . ' status: ' . $status, $id_plagiat);
        $this->session->set_flashdata('pesan_sukses', 'Verifikasi berhasil: ' . $status);
        redirect('operator/skripsi/plagiarisme');
    }
}