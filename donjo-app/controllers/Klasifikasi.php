<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Klasifikasi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		session_start();
		$this->load->model('user_model');
		$this->grup = $this->user_model->sesi_grup($_SESSION['sesi']);
		if ($this->grup != (1 or 2 or 3))
		{
			if (empty($this->grup))
				$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
			else
				unset($_SESSION['request_uri']);
			redirect('siteman');
		}
		$this->load->model('header_model');
		$this->load->model('klasifikasi_model');
		$this->modul_ini = 15;
		$this->controller = 'klasifikasi';
	}

	public function clear()
	{
		$_SESSION['per_page'] = 50;
		unset($_SESSION['cari']);
		unset($_SESSION['filter']);
		redirect('klasifikasi');
	}

	public function index($p=1, $o=0)
	{
		$data['p'] = $p;
		$data['o'] = $o;

		if (isset($_SESSION['cari']))
			$data['cari'] = $_SESSION['cari'];
		else $data['cari'] = '';

		if (isset($_SESSION['filter']))
			$data['filter'] = $_SESSION['filter'];
		else $data['filter'] = '';

		if (isset($_POST['per_page']))
			$_SESSION['per_page'] = $_POST['per_page'];
		$data['per_page'] = $_SESSION['per_page'];

		$data['paging'] = $this->klasifikasi_model->paging($p, $o);
		$data['main'] = $this->klasifikasi_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
		$data['keyword'] = $this->klasifikasi_model->autocomplete();

		$header = $this->header_model->get_data();
		$nav['act_sub'] = 63;
		$this->load->view('header', $header);
		$this->load->view('nav', $nav);
		$this->load->view('klasifikasi/table', $data);
		$this->load->view('footer');
	}

	public function form($p=1, $o=0, $id='')
	{
		if (!punya_akses($this->grup, array(1)))
			redirect("klasifikasi/index/$p/$o");

		$data['p'] = $p;
		$data['o'] = $o;

		if ($id)
		{
			$data['data'] = $this->klasifikasi_model->get_klasifikasi($id);
			$data['form_action'] = site_url("klasifikasi/update/$id/$p/$o");
		}
		else
		{
			$data['data'] = null;
			$data['form_action'] = site_url("klasifikasi/insert");
		}
		$header = $this->header_model->get_data();

		$nav['act_sub'] = 63;
		$this->load->view('header', $header);
		$this->load->view('nav', $nav);
		$this->load->view('klasifikasi/form', $data);
		$this->load->view('footer');
	}

	public function search()
	{
		$cari = $this->input->post('cari');
		if ($cari != '')
			$_SESSION['cari'] = $cari;
		else unset($_SESSION['cari']);
		redirect('klasifikasi');
	}

	public function filter()
	{
		$filter = $this->input->post('filter');
		if ($filter != "")
			$_SESSION['filter'] = $filter;
		else unset($_SESSION['filter']);
		redirect("klasifikasi");
	}

	public function insert()
	{
		if (!punya_akses($this->grup, array(1)))
			redirect("klasifikasi");
		$_SESSION['success'] = 1;
		$outp = $this->klasifikasi_model->insert();
		if (!$outp) $_SESSION['success'] = -1;
		redirect("klasifikasi");
	}

	public function update($id='', $p=1, $o=0)
	{
		if (!punya_akses($this->grup, array(1)))
			redirect("klasifikasi/index/$p/$o");

		$_SESSION['success'] = 1;
		$outp = $this->klasifikasi_model->update($id);
		if (!$outp) $_SESSION['success'] = -1;
		redirect("klasifikasi/index/$p/$o");
	}

	public function delete($p=1, $o=0, $id='')
	{
		if (!punya_akses($this->grup, array(1)))
			redirect("klasifikasi/index/$p/$o");

		$_SESSION['success'] = 1;
		$this->klasifikasi_model->delete($id);
		redirect("klasifikasi/index/$p/$o");
	}

	public function delete_all($p=1, $o=0)
	{
		if (!punya_akses($this->grup, array(1)))
			redirect("klasifikasi/index/$p/$o");

		$_SESSION['success'] = 1;
		$this->klasifikasi_model->delete_all();
		redirect("klasifikasi/index/$p/$o");
	}

	public function lock($p=1, $o=0, $id='')
	{
		if (!punya_akses($this->grup, array(1)))
			redirect("klasifikasi/index/$p/$o");

		$this->klasifikasi_model->lock($id, 0);
		redirect("klasifikasi/index/$p/$o");
	}

	public function unlock($p=1, $o=0, $id='')
	{
		if (!punya_akses($this->grup, array(1)))
			redirect("klasifikasi/index/$p/$o");

		$this->klasifikasi_model->lock($id, 1);
		redirect("klasifikasi/index/$p/$o");
	}

}