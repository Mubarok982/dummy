<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role') != 'operator') redirect('auth/login');
        $this->load->model('operator/M_laporan_opt');
        $this->load->model('M_Log');
    }

    // --- MONITORING PROGRES ---
    public function monitoring()
    {
        $data['title'] = 'Monitoring Progres';
        $this->load->library('pagination');

        $prodi = $this->input->get('prodi');
        $keyword = $this->input->get('keyword');

        $config['base_url'] = base_url('operator/laporan/monitoring');
        $config['total_rows'] = $this->M_laporan_opt->count_laporan_progres($prodi, $keyword);
        $config['per_page'] = 15;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';

        // Config Pagination Bootstrap
        $config['full_tag_open']    = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close']   = '</ul>';
        $config['first_link']       = '&laquo;';
        $config['last_link']        = '&raquo;';
        $config['first_tag_open']   = '<li class="page-item">';
        $config['first_tag_close']  = '</li>';
        $config['prev_link']        = '&lsaquo;';
        $config['prev_tag_open']    = '<li class="page-item">';
        $config['prev_tag_close']   = '</li>';
        $config['next_link']        = '&rsaquo;';
        $config['next_tag_open']    = '<li class="page-item">';
        $config['next_tag_close']   = '</li>';
        $config['last_tag_open']    = '<li class="page-item">';
        $config['last_tag_close']   = '</li>';
        $config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']    = '</span></li>';
        $config['num_tag_open']     = '<li class="page-item">';
        $config['num_tag_close']    = '</li>';
        $config['attributes']       = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['laporan'] = $this->M_laporan_opt->get_laporan_progres($prodi, $keyword, $config['per_page'], $page);
        
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start_index'] = $page;

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_monitoring_progres', $data);
        $this->load->view('template/footer');
    }

    // --- KINERJA DOSEN ---
    public function kinerja()
    {
        $data['title'] = 'Kinerja Dosen';
        $this->load->library('pagination');

        $keyword = $this->input->get('keyword');

        $config['base_url'] = base_url('operator/laporan/kinerja');
        $config['total_rows'] = $this->M_laporan_opt->count_dosen_pembimbing($keyword);
        $config['per_page'] = 10;
        $config['reuse_query_string'] = TRUE;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        
        // (Copy config pagination dari atas agar tidak panjang)
        $config['full_tag_open'] = '<ul class="pagination pagination-sm m-0 float-right">'; $config['full_tag_close'] = '</ul>'; $config['first_link'] = '&laquo;'; $config['last_link'] = '&raquo;'; $config['first_tag_open'] = '<li class="page-item">'; $config['first_tag_close'] = '</li>'; $config['prev_link'] = '&lsaquo;'; $config['prev_tag_open'] = '<li class="page-item">'; $config['prev_tag_close'] = '</li>'; $config['next_link'] = '&rsaquo;'; $config['next_tag_open'] = '<li class="page-item">'; $config['next_tag_close'] = '</li>'; $config['last_tag_open'] = '<li class="page-item">'; $config['last_tag_close'] = '</li>'; $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">'; $config['cur_tag_close'] = '</span></li>'; $config['num_tag_open'] = '<li class="page-item">'; $config['num_tag_close'] = '</li>'; $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = $this->input->get('page') ? $this->input->get('page') : 0;
        $data['dosen_list'] = $this->M_laporan_opt->get_dosen_pembimbing_list($keyword, $config['per_page'], $page);
        
        foreach ($data['dosen_list'] as $key => $dosen) {
            $aktivitas = $this->M_Log->get_dosen_activity_summary($dosen['id']);
            $data['dosen_list'][$key]['aktivitas'] = $aktivitas;
            $total = 0;
            foreach($aktivitas as $act) { $total += $act['total_aksi']; }
            $data['dosen_list'][$key]['total_aksi'] = $total;
        }

        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start_index'] = $page;
        $data['per_page'] = $config['per_page'];

        $this->load->view('template/header', $data);
        $this->load->view('template/sidebar', $data);
        $this->load->view('operator/v_kinerja_dosen', $data);
        $this->load->view('template/footer');
    }

    public function kinerja_csv()
    {
        $keyword = $this->input->get('keyword');
        $dosen_list = $this->M_laporan_opt->get_dosen_pembimbing_list($keyword, NULL, NULL);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Laporan_Kinerja_Dosen_'.date('Y-m-d').'.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('No', 'Nama Dosen', 'NIDK', 'Total Aktivitas Koreksi'));

        $no = 1;
        foreach ($dosen_list as $dosen) {
            $aktivitas = $this->M_Log->get_dosen_activity_summary($dosen['id']);
            $total = 0;
            foreach($aktivitas as $act) { $total += $act['total_aksi']; }

            fputcsv($output, array($no++, $dosen['nama'], "'".$dosen['nidk'], $total));
        }
        fclose($output);
    }
}